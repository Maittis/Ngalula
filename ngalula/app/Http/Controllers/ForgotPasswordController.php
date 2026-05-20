<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Send password reset link via email
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status == Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Password reset link sent successfully']);
        }

        return response()->json(['message' => 'Unable to send password reset link'], 500);
    }

    /**
     * Send password reset code via SMS
     */
    public function sendResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:users,phone',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('phone', $request->phone)->first();
        
        // Generate a reset code (different from OTP)
        $resetCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'otp_code' => $resetCode,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        // TODO: Send reset code via SMS service
        // $this->sendSMS($user->phone, "Your password reset code is: $resetCode");

        return response()->json([
            'message' => 'Password reset code sent successfully',
            'expires_at' => $user->otp_expires_at,
        ]);
    }

    /**
     * Reset password using code
     */
    public function resetPasswordWithCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:users,phone',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if ($user->otp_code !== $request->code || $user->otp_expires_at->isPast()) {
            return response()->json(['message' => 'Invalid or expired reset code'], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        // Revoke all tokens for security
        $user->tokens()->delete();

        return response()->json(['message' => 'Password reset successfully']);
    }

    /**
     * Reset password using token (email method)
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password', 'password_confirmation', 'token');
        
        $status = Password::broker()->reset($credentials, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->setRememberToken(Str::random(60));
            $user->save();
            
            // Revoke all tokens for security
            $user->tokens()->delete();
        });

        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successfully']);
        }

        return response()->json(['message' => 'Unable to reset password'], 500);
    }
}
