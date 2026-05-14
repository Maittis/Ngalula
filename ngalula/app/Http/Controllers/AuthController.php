<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * User registration (API)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|unique:users',
            'user_type' => 'required|in:customer,therapist,receptionist,admin,super_admin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Additional validation for admin roles - requires admin authorization key
        if (in_array($request->user_type, ['admin', 'super_admin'])) {
            $adminKey = $request->input('admin_key');
            $validKeys = array_map('trim', explode(',', env('ADMIN_AUTH_KEYS', 'admin-secret-key-2024')));
            if (!$adminKey || !in_array($adminKey, $validKeys)) {
                return response()->json(['errors' => ['admin_key' => ['Invalid or missing admin authorization key.']]], 422);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'user_type' => $request->user_type,
        ]);

        // Generate OTP for phone verification
        $otp = $user->generateOTP();

        // TODO: Send OTP via SMS service (Twilio, etc.)
        // $this->sendSMS($user->phone, "Your verification code is: $otp");

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully. Please verify your phone number.',
            'user' => $user,
            'token' => $token,
            'otp_sent' => true,
        ], 201);
    }

    /**
     * User login (API)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if login is email or phone
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        
        $credentials = [
            $loginField => $request->login,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            return response()->json(['message' => 'Account is deactivated'], 401);
        }

        // Check if phone is verified for non-social login
        if (!$user->isPhoneVerified() && !$user->google_id && !$user->facebook_id && !$user->apple_id) {
            return response()->json([
                'message' => 'Please verify your phone number first',
                'phone_verification_required' => true
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
            'requires_2fa' => $user->two_factor_enabled,
        ]);
    }

    /**
     * Send OTP verification
     */
    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:users,phone',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('phone', $request->phone)->first();
        $otp = $user->generateOTP();

        // TODO: Send OTP via SMS service
        // $this->sendSMS($user->phone, "Your verification code is: $otp");

        return response()->json([
            'message' => 'OTP sent successfully',
            'otp_expires_at' => $user->otp_expires_at,
        ]);
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:users,phone',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if ($user->verifyOTP($request->otp)) {
            return response()->json([
                'message' => 'Phone number verified successfully',
                'phone_verified' => true,
            ]);
        }

        return response()->json(['message' => 'Invalid or expired OTP'], 422);
    }

    /**
     * Social login
     */
    public function socialLogin(Request $request, string $provider)
    {
        $validator = Validator::make($request->all(), [
            'access_token' => 'required|string',
            'user_type' => 'sometimes|required|in:customer,therapist,receptionist,admin,super_admin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $socialUser = $this->getSocialUser($provider, $request->access_token);
            
            if (!$socialUser) {
                return response()->json(['message' => 'Invalid social token'], 401);
            }

            $providerField = $provider . '_id';
            $user = User::where($providerField, $socialUser['id'])->first();

            if (!$user) {
                // Create new user from social login
                $user = User::create([
                    'name' => $socialUser['name'],
                    'email' => $socialUser['email'],
                    $providerField => $socialUser['id'],
                    'user_type' => $request->user_type ?? 'customer',
                    'phone_verified_at' => now(), // Auto-verify for social login
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Social login successful',
                'user' => $user,
                'token' => $token,
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Social login failed'], 500);
        }
    }

    /**
     * Enable two-factor authentication
     */
    public function enable2FA(Request $request)
    {
        $user = Auth::user();
        
        // Generate 2FA secret
        $secret = $this->generate2FASecret();
        
        $user->update([
            'two_factor_secret' => encrypt($secret),
            'two_factor_enabled' => true,
        ]);

        return response()->json([
            'message' => '2FA enabled',
            'qr_code' => $this->generate2FAQRCode($secret, $user->email),
            'secret' => $secret,
        ]);
    }

    /**
     * Verify 2FA code
     */
    public function verify2FA(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        
        if (!$user->two_factor_enabled) {
            return response()->json(['message' => '2FA not enabled'], 400);
        }

        if ($this->verify2FACode($user->two_factor_secret, $request->code)) {
            return response()->json(['message' => '2FA verification successful']);
        }

        return response()->json(['message' => 'Invalid 2FA code'], 422);
    }

    // ==========================================
    // ROLE-SPECIFIC LOGIN FORMS
    // ==========================================

    /**
     * Show customer login form
     */
    public function showCustomerLoginForm()
    {
        return view('auth.customer.login');
    }

    /**
     * Show therapist login form
     */
    public function showTherapistLoginForm()
    {
        return view('auth.therapist.login');
    }

    /**
     * Show admin login form
     */
    public function showAdminLoginForm()
    {
        return view('auth.admin.login');
    }

    // ==========================================
    // ROLE-SPECIFIC REGISTRATION FORMS
    // ==========================================

    /**
     * Show customer registration form
     */
    public function showCustomerRegistrationForm()
    {
        return view('auth.customer.register');
    }

    /**
     * Show therapist registration form
     */
    public function showTherapistRegistrationForm()
    {
        return view('auth.therapist.register');
    }

    /**
     * Show admin registration form
     */
    public function showAdminRegistrationForm()
    {
        return view('auth.admin.register');
    }

    // ==========================================
    // ROLE-SPECIFIC LOGIN HANDLERS
    // ==========================================

    /**
     * Handle customer login
     */
    public function handleCustomerLogin(Request $request)
    {
        return $this->authenticateByRole($request, 'customer');
    }

    /**
     * Handle therapist login
     */
    public function handleTherapistLogin(Request $request)
    {
        return $this->authenticateByRole($request, 'therapist');
    }

    /**
     * Handle admin login
     */
    public function handleAdminLogin(Request $request)
    {
        return $this->authenticateByRole($request, ['admin', 'super_admin']);
    }

    // ==========================================
    // ROLE-SPECIFIC REGISTRATION HANDLERS
    // ==========================================

    /**
     * Handle customer registration
     */
    public function handleCustomerRegister(Request $request)
    {
        return $this->registerByRole($request, 'customer');
    }

    /**
     * Handle therapist registration
     */
    public function handleTherapistRegister(Request $request)
    {
        return $this->registerByRole($request, 'therapist');
    }

    /**
     * Handle admin registration
     */
    public function handleAdminRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admin_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors(['admin_key' => 'Admin authorization key is required.'])->withInput();
        }

        // Verify admin authorization key from environment configuration
        $validKeys = array_map('trim', explode(',', env('ADMIN_AUTH_KEYS', 'admin-secret-key-2024')));
        if (!in_array($request->admin_key, $validKeys)) {
            return back()->withErrors(['admin_key' => 'Invalid admin authorization key.'])->withInput();
        }

        return $this->registerByRole($request, 'admin');
    }

    /**
     * Authenticate user by role
     */
    private function authenticateByRole(Request $request, $allowedRoles)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            $allowedRoles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];

            // Check if user has the correct role
            if (!in_array($user->user_type, $allowedRoles)) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'This account does not have the required permissions for this portal.',
                ])->onlyInput('email');
            }

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'This account has been deactivated.',
                ])->onlyInput('email');
            }

            // Redirect based on user type
            switch ($user->user_type) {
                case 'therapist':
                    return redirect()->route('therapist.dashboard')->with('success', 'Welcome back!');
                case 'admin':
                case 'super_admin':
                    return redirect()->route('admin.dashboard')->with('success', 'Welcome back!');
                case 'receptionist':
                    return redirect()->route('receptionist.dashboard')->with('success', 'Welcome back!');
                case 'customer':
                    return redirect()->route('customer.dashboard')->with('success', 'Welcome back!');
                default:
                    return redirect()->intended('/');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Register user by role
     */
    private function registerByRole(Request $request, string $userType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|unique:users',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'user_type' => $userType,
        ]);

        // Auto-login the user after registration
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect based on user type
        switch ($userType) {
            case 'therapist':
                return redirect()->route('therapist.dashboard')->with('success', 'Account created successfully! Welcome to the team.');
            case 'admin':
            case 'super_admin':
                return redirect()->route('admin.dashboard')->with('success', 'Admin account created successfully!');
            case 'receptionist':
                return redirect()->route('receptionist.dashboard')->with('success', 'Account created successfully!');
            default:
                return redirect()->route('customer.dashboard')->with('success', 'Welcome! Your account has been created.');
        }
    }

    /**
     * Handle web login (generic - kept for backward compatibility)
     */
    public function webLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirect based on user type
            $user = Auth::user();
            switch ($user->user_type) {
                case 'therapist':
                    return redirect()->route('therapist.dashboard');
                case 'admin':
                case 'super_admin':
                    return redirect()->route('admin.dashboard');
                case 'receptionist':
                    return redirect()->route('receptionist.dashboard');
                case 'customer':
                    return redirect()->route('customer.dashboard');
                default:
                    return redirect()->intended('/');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle web registration (generic - kept for backward compatibility)
     */
    public function webRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|unique:users',
            'user_type' => 'required|in:customer,therapist,receptionist,admin,super_admin',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'user_type' => $request->user_type,
        ]);

        // Auto-login the user after registration
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect based on user type
        switch ($user->user_type) {
            case 'therapist':
                return redirect()->route('therapist.dashboard')->with('success', 'Account created successfully!');
            case 'admin':
            case 'super_admin':
                return redirect()->route('admin.dashboard')->with('success', 'Account created successfully!');
            case 'receptionist':
                return redirect()->route('receptionist.dashboard')->with('success', 'Account created successfully!');
            default:
                return redirect()->route('customer.dashboard')->with('success', 'Account created successfully!');
        }
    }

    /**
     * Show login form (generic - kept for backward compatibility)
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Show registration form (generic - kept for backward compatibility)
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Logout (API)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Logout (Web)
     */
    public function webLogout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Enable biometric authentication
     */
    public function enableBiometric(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'biometric_data' => 'required|string',
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        
        // Generate biometric token based on device and biometric data
        $biometricToken = hash('sha256', $user->id . $request->device_id . $request->biometric_data);
        
        $user->update([
            'biometric_token' => $biometricToken,
        ]);

        return response()->json([
            'message' => 'Biometric authentication enabled',
            'biometric_token' => $biometricToken,
        ]);
    }

    /**
     * Biometric login
     */
    public function biometricLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'biometric_data' => 'required|string',
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user->biometric_token) {
            return response()->json(['message' => 'Biometric authentication not enabled for this account'], 400);
        }

        // Verify biometric token
        $expectedToken = hash('sha256', $user->id . $request->device_id . $request->biometric_data);
        
        if ($user->biometric_token !== $expectedToken) {
            return response()->json(['message' => 'Invalid biometric data'], 401);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'Account is deactivated'], 401);
        }

        $token = $user->createToken('biometric_auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Biometric login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Disable biometric authentication
     */
    public function disableBiometric(Request $request)
    {
        $user = Auth::user();
        
        $user->update([
            'biometric_token' => null,
        ]);

        return response()->json(['message' => 'Biometric authentication disabled']);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Helper method to get social user data
     */
    private function getSocialUser(string $provider, string $accessToken)
    {
        // TODO: Implement actual social API calls
        // This is a placeholder implementation
        
        switch ($provider) {
            case 'google':
                $response = Http::get("https://www.googleapis.com/oauth2/v2/userinfo", [
                    'access_token' => $accessToken,
                ]);
                break;
                
            case 'facebook':
                $response = Http::get("https://graph.facebook.com/me", [
                    'fields' => 'id,name,email',
                    'access_token' => $accessToken,
                ]);
                break;
                
            default:
                return null;
        }

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Generate 2FA secret
     */
    private function generate2FASecret(): string
    {
        return strtoupper(Str::random(16));
    }

    /**
     * Generate 2FA QR code
     */
    private function generate2FAQRCode(string $secret, string $email): string
    {
        // TODO: Implement QR code generation
        return "otpauth://totp/SpaApp:$email?secret=$secret&issuer=SpaApp";
    }

    /**
     * Verify 2FA code
     */
    private function verify2FACode(string $encryptedSecret, string $code): bool
    {
        // TODO: Implement actual TOTP verification
        return true; // Placeholder
    }
}
