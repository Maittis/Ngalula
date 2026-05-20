<?php

namespace App\Http\Controllers;

use App\Models\Therapist;
use App\Models\Skill;
use App\Models\Specialization;
use App\Models\WorkingSchedule;
use App\Models\AvailabilitySlot;
use App\Models\AttendanceRecord;
use App\Models\LeaveRequest;
use App\Models\TherapistRating;
use App\Models\PerformanceMetric;
use App\Models\CommissionRecord;
use App\Models\TherapistNote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TherapistController extends Controller
{
    /**
     * Get all therapists with filtering and pagination
     */
    public function index(Request $request)
    {
        $query = Therapist::with(['user', 'skills', 'specializations']);

        // Filter by status
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Filter by employment type
        if ($request->has('employment_type')) {
            $query->byEmploymentType($request->employment_type);
        }

        // Filter by specialization
        if ($request->has('specialization_id')) {
            $query->bySpecialization($request->specialization_id);
        }

        // Filter by skill
        if ($request->has('skill_id')) {
            $query->bySkill($request->skill_id);
        }

        // Filter by minimum rating
        if ($request->has('min_rating')) {
            $query->byRating($request->min_rating);
        }

        // Filter by verified license
        if ($request->has('verified')) {
            $query->verified();
        }

        // Filter by accepting new clients
        if ($request->has('accepting_new_clients')) {
            $query->acceptingNewClients();
        }

        // Search by name or license number
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                })->orWhere('license_number', 'like', "%{$search}%");
            });
        }

        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $therapists = $query->paginate(20);

        return response()->json([
            'therapists' => $therapists,
            'filters' => [
                'statuses' => Therapist::getStatuses(),
                'employment_types' => Therapist::getEmploymentTypes(),
                'specializations' => Specialization::active()->get(),
                'skills' => Skill::active()->get(),
            ],
        ]);
    }

    /**
     * Get therapist details
     */
    public function show($id)
    {
        $therapist = Therapist::with([
            'user',
            'skills',
            'specializations',
            'workingSchedules',
            'ratings' => function ($query) {
                $query->with('client')->latest()->limit(10);
            },
            'performanceMetrics' => function ($query) {
                $query->latest()->limit(20);
            },
            'commissionRecords' => function ($query) {
                $query->latest()->limit(10);
            },
            'notes' => function ($query) {
                $query->with('author')->latest()->limit(10);
            }
        ])->findOrFail($id);

        return response()->json([
            'therapist' => $therapist,
            'profile_summary' => $therapist->getProfileSummary(),
            'rating_distribution' => TherapistRating::getRatingDistribution($id),
            'commission_summary' => CommissionRecord::getCommissionSummary($id),
            'notes_summary' => TherapistNote::getNotesSummary($id),
        ]);
    }

    /**
     * Create new therapist
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|unique:therapists,user_id',
            'license_number' => 'required|string|unique:therapists,license_number',
            'professional_title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'hire_date' => 'required|date',
            'employment_type' => 'required|in:' . implode(',', array_keys(Therapist::getEmploymentTypes())),
            'status' => 'required|in:' . implode(',', array_keys(Therapist::getStatuses())),
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'years_of_experience' => 'nullable|integer|min:0',
            'education' => 'nullable|string',
            'certifications' => 'nullable|string',
            'languages' => 'nullable|array',
            'hourly_rate' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'bank_account' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'accepts_new_clients' => 'boolean',
            'working_days' => 'nullable|array',
            'preferred_start_time' => 'nullable|date_format:H:i',
            'preferred_end_time' => 'nullable|date_format:H:i',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'emergency_contact_relationship' => 'nullable|string',
            'skills' => 'nullable|array',
            'skills.*.id' => 'required|exists:skills,id',
            'skills.*.proficiency_level' => 'required|in:' . implode(',', array_keys(Skill::getProficiencyLevels())),
            'skills.*.years_experience' => 'nullable|integer|min:0',
            'skills.*.certified' => 'boolean',
            'specializations' => 'nullable|array',
            'specializations.*.id' => 'required|exists:specializations,id',
            'specializations.*.primary' => 'boolean',
            'specializations.*.years_experience' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $therapist = Therapist::create($request->except(['skills', 'specializations']));

            // Attach skills
            if ($request->has('skills')) {
                foreach ($request->skills as $skill) {
                    $therapist->addSkill(
                        $skill['id'],
                        $skill['proficiency_level'],
                        $skill['years_experience'] ?? 0,
                        $skill['certified'] ?? false
                    );
                }
            }

            // Attach specializations
            if ($request->has('specializations')) {
                foreach ($request->specializations as $specialization) {
                    $therapist->addSpecialization(
                        $specialization['id'],
                        $specialization['primary'] ?? false,
                        $specialization['years_experience'] ?? 0
                    );
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Therapist created successfully',
                'therapist' => $therapist->load(['user', 'skills', 'specializations']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to create therapist',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update therapist
     */
    public function update(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'license_number' => 'required|string|unique:therapists,license_number,' . $id,
            'professional_title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'hire_date' => 'required|date',
            'termination_date' => 'nullable|date|after_or_equal:hire_date',
            'employment_type' => 'required|in:' . implode(',', array_keys(Therapist::getEmploymentTypes())),
            'status' => 'required|in:' . implode(',', array_keys(Therapist::getStatuses())),
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'years_of_experience' => 'nullable|integer|min:0',
            'education' => 'nullable|string',
            'certifications' => 'nullable|string',
            'languages' => 'nullable|array',
            'hourly_rate' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'bank_account' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'accepts_new_clients' => 'boolean',
            'working_days' => 'nullable|array',
            'preferred_start_time' => 'nullable|date_format:H:i',
            'preferred_end_time' => 'nullable|date_format:H:i',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'emergency_contact_relationship' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $therapist->update($request->all());

        return response()->json([
            'message' => 'Therapist updated successfully',
            'therapist' => $therapist->fresh(),
        ]);
    }

    /**
     * Delete therapist
     */
    public function destroy($id)
    {
        $therapist = Therapist::findOrFail($id);
        
        // Check if therapist has active appointments or pending commissions
        if ($therapist->commissionRecords()->unpaid()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete therapist with unpaid commissions',
            ], 400);
        }

        $therapist->delete();

        return response()->json([
            'message' => 'Therapist deleted successfully',
        ]);
    }

    /**
     * Get therapist working schedules
     */
    public function getWorkingSchedules($id)
    {
        $therapist = Therapist::findOrFail($id);
        $schedules = $therapist->workingSchedules()->with('therapist')->get();

        return response()->json([
            'schedules' => $schedules,
            'days_of_week' => WorkingSchedule::getDaysOfWeek(),
        ]);
    }

    /**
     * Create working schedule
     */
    public function createWorkingSchedule(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'day_of_week' => 'required|in:' . implode(',', array_keys(WorkingSchedule::getDaysOfWeek())),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'boolean',
            'break_start_time' => 'nullable|date_format:H:i|after:start_time|before:end_time',
            'break_end_time' => 'nullable|date_format:H:i|after:break_start_time|before:end_time',
            'max_appointments' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $schedule = $therapist->workingSchedules()->create($request->all());

        return response()->json([
            'message' => 'Working schedule created successfully',
            'schedule' => $schedule,
        ], 201);
    }

    /**
     * Get therapist availability slots
     */
    public function getAvailabilitySlots(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);
        $query = $therapist->availabilitySlots();

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->betweenDates($request->start_date, $request->end_date);
        }

        // Filter by status
        if ($request->has('available')) {
            if ($request->boolean('available')) {
                $query->available();
            } else {
                $query->booked();
            }
        }

        $slots = $query->orderBy('date')->orderBy('start_time')->get();

        return response()->json([
            'slots' => $slots,
        ]);
    }

    /**
     * Create availability slot
     */
    public function createAvailabilitySlot(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check for conflicts
        $conflictingSlots = $therapist->availabilitySlots()
            ->where('date', $request->date)
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>', $request->start_time);
            })
            ->count();

        if ($conflictingSlots > 0) {
            return response()->json([
                'message' => 'Time slot conflicts with existing availability',
            ], 400);
        }

        $slot = $therapist->createAvailabilitySlot(
            $request->date,
            $request->start_time,
            $request->end_time,
            $request->boolean('is_available', true)
        );

        if ($request->has('notes')) {
            $slot->update(['notes' => $request->notes]);
        }

        return response()->json([
            'message' => 'Availability slot created successfully',
            'slot' => $slot,
        ], 201);
    }

    /**
     * Get therapist attendance records
     */
    public function getAttendanceRecords(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);
        $query = $therapist->attendanceRecords()->with(['approvedBy']);

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->betweenDates($request->start_date, $request->end_date);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        $records = $query->orderBy('date', 'desc')->paginate(50);

        return response()->json([
            'records' => $records,
            'statuses' => AttendanceRecord::getStatuses(),
        ]);
    }

    /**
     * Record attendance
     */
    public function recordAttendance(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'date' => 'required|date|before_or_equal:today',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:' . implode(',', array_keys(AttendanceRecord::getStatuses())),
            'break_start' => 'nullable|date_format:H:i|after:check_in|before:check_out',
            'break_end' => 'nullable|date_format:H:i|after:break_start|before:check_out',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if record already exists for this date
        $existingRecord = $therapist->attendanceRecords()->byDate($request->date)->first();
        if ($existingRecord) {
            return response()->json([
                'message' => 'Attendance record already exists for this date',
                'record' => $existingRecord,
            ], 400);
        }

        $record = $therapist->attendanceRecords()->create($request->all());

        return response()->json([
            'message' => 'Attendance recorded successfully',
            'record' => $record,
        ], 201);
    }

    /**
     * Get therapist leave requests
     */
    public function getLeaveRequests(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);
        $query = $therapist->leaveRequests()->with(['approvedBy', 'rejectedBy']);

        // Filter by status
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->betweenDates($request->start_date, $request->end_date);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'requests' => $requests,
            'types' => LeaveRequest::getTypes(),
            'statuses' => LeaveRequest::getStatuses(),
        ]);
    }

    /**
     * Create leave request
     */
    public function createLeaveRequest(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:' . implode(',', array_keys(LeaveRequest::getTypes())),
            'reason' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'emergency_contact' => 'nullable|string',
            'coverage_arranged' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('attachment');

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('leave_attachments', 'public');
            $data['attachment'] = $path;
        }

        $leaveRequest = $therapist->leaveRequests()->create($data);

        return response()->json([
            'message' => 'Leave request created successfully',
            'request' => $leaveRequest,
        ], 201);
    }

    /**
     * Get therapist ratings
     */
    public function getRatings(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);
        $query = $therapist->ratings()->with(['client', 'respondedBy']);

        // Filter by rating range
        if ($request->has('min_rating')) {
            $query->byRating($request->min_rating, $request->get('max_rating'));
        }

        // Filter by verified
        if ($request->has('verified')) {
            $query->verified();
        }

        // Filter by comments
        if ($request->has('with_comments')) {
            $query->withComment();
        }

        $ratings = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'ratings' => $ratings,
            'rating_distribution' => TherapistRating::getRatingDistribution($id),
            'average_rating' => TherapistRating::getAverageRating($id),
            'total_ratings' => TherapistRating::getTotalRatings($id),
        ]);
    }

    /**
     * Add therapist rating
     */
    public function addRating(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:users,id',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string',
            'anonymous' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if client already rated this therapist for the same appointment
        if ($request->has('appointment_id')) {
            $existingRating = $therapist->ratings()
                ->where('client_id', $request->client_id)
                ->where('appointment_id', $request->appointment_id)
                ->first();

            if ($existingRating) {
                return response()->json([
                    'message' => 'You have already rated this therapist for this appointment',
                ], 400);
            }
        }

        $rating = $therapist->addRating(
            $request->client_id,
            $request->rating,
            $request->comment
        );

        if ($request->has('anonymous')) {
            $rating->update(['anonymous' => $request->boolean('anonymous')]);
        }

        // Update therapist average rating
        $therapist->updateRating();

        return response()->json([
            'message' => 'Rating added successfully',
            'rating' => $rating,
        ], 201);
    }

    /**
     * Get therapist performance metrics
     */
    public function getPerformanceMetrics(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);
        $query = $therapist->performanceMetrics()->with('createdBy');

        // Filter by type
        if ($request->has('metric_type')) {
            $query->byType($request->metric_type);
        }

        // Filter by period
        if ($request->has('period')) {
            $query->byPeriod($request->period);
        }

        $metrics = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json([
            'metrics' => $metrics,
            'metric_types' => PerformanceMetric::getMetricTypes(),
            'periods' => PerformanceMetric::getPeriods(),
            'summary' => PerformanceMetric::getMetricsSummary($id),
        ]);
    }

    /**
     * Add performance metric
     */
    public function addPerformanceMetric(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'metric_type' => 'required|in:' . implode(',', array_keys(PerformanceMetric::getMetricTypes())),
            'value' => 'required|numeric',
            'period' => 'required|in:' . implode(',', array_keys(PerformanceMetric::getPeriods())),
            'target_value' => 'nullable|numeric',
            'benchmark' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $metric = $therapist->addPerformanceMetric(
            $request->metric_type,
            $request->value,
            $request->period,
            $request->notes
        );

        if ($request->has('target_value')) {
            $metric->update(['target_value' => $request->target_value]);
        }

        if ($request->has('benchmark')) {
            $metric->update(['benchmark' => $request->benchmark]);
        }

        return response()->json([
            'message' => 'Performance metric added successfully',
            'metric' => $metric,
        ], 201);
    }

    /**
     * Get therapist commission records
     */
    public function getCommissionRecords(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);
        $query = $therapist->commissionRecords()->with(['paidBy']);

        // Filter by status
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->byType($request->type);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->betweenDates($request->start_date, $request->end_date);
        }

        $records = $query->orderBy('calculated_at', 'desc')->paginate(50);

        return response()->json([
            'records' => $records,
            'types' => CommissionRecord::getTypes(),
            'statuses' => CommissionRecord::getStatuses(),
            'payment_methods' => CommissionRecord::getPaymentMethods(),
            'summary' => CommissionRecord::getCommissionSummary($id),
        ]);
    }

    /**
     * Add commission record
     */
    public function addCommissionRecord(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:' . implode(',', array_keys(CommissionRecord::getTypes())),
            'description' => 'required|string',
            'base_amount' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:1',
            'related_id' => 'nullable|integer',
            'related_type' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $commission = $therapist->addCommission(
            $request->amount,
            $request->type,
            $request->description,
            $request->related_id
        );

        if ($request->has('base_amount') && $request->has('commission_rate')) {
            $commission->recalculate($request->base_amount, $request->commission_rate);
        }

        if ($request->has('related_type')) {
            $commission->update(['related_type' => $request->related_type]);
        }

        if ($request->has('notes')) {
            $commission->update(['notes' => $request->notes]);
        }

        return response()->json([
            'message' => 'Commission record added successfully',
            'commission' => $commission,
        ], 201);
    }

    /**
     * Get therapist notes
     */
    public function getNotes(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);
        $query = $therapist->notes()->with(['author', 'resolvedBy']);

        // Filter by type
        if ($request->has('type')) {
            $query->byType($request->type);
        }

        // Filter by privacy
        if ($request->has('private')) {
            if ($request->boolean('private')) {
                $query->private();
            } else {
                $query->public();
            }
        }

        // Filter by importance
        if ($request->has('important')) {
            $query->important();
        }

        // Filter by resolution status
        if ($request->has('resolved')) {
            if ($request->boolean('resolved')) {
                $query->resolved();
            } else {
                $query->unresolved();
            }
        }

        $notes = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json([
            'notes' => $notes,
            'types' => TherapistNote::getTypes(),
            'summary' => TherapistNote::getNotesSummary($id),
        ]);
    }

    /**
     * Add therapist note
     */
    public function addNote(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'type' => 'required|in:' . implode(',', array_keys(TherapistNote::getTypes())),
            'is_private' => 'boolean',
            'is_important' => 'boolean',
            'follow_up_required' => 'boolean',
            'follow_up_date' => 'nullable|date|after:today',
            'tags' => 'nullable|array',
            'attachments' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $note = $therapist->addNote(
            $request->content,
            $request->type,
            auth()->id()
        );

        // Update additional fields
        $updateData = [];
        if ($request->has('is_private')) {
            $updateData['is_private'] = $request->boolean('is_private');
        }
        if ($request->has('is_important')) {
            $updateData['is_important'] = $request->boolean('is_important');
        }
        if ($request->has('follow_up_required')) {
            $updateData['follow_up_required'] = $request->boolean('follow_up_required');
        }
        if ($request->has('follow_up_date')) {
            $updateData['follow_up_date'] = $request->follow_up_date;
        }
        if ($request->has('tags')) {
            $updateData['tags'] = $request->tags;
        }
        if ($request->has('attachments')) {
            $updateData['attachments'] = $request->attachments;
        }

        if (!empty($updateData)) {
            $note->update($updateData);
        }

        return response()->json([
            'message' => 'Note added successfully',
            'note' => $note->load(['author']),
        ], 201);
    }

    /**
     * Get therapist statistics
     */
    public function getStatistics(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);
        $period = $request->get('period', 'monthly');

        $stats = [
            'profile' => $therapist->getProfileSummary(),
            'attendance' => [
                'this_month' => $therapist->attendanceRecords()
                    ->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year)
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status'),
                'total_present' => $therapist->attendanceRecords()->present()->count(),
                'total_absent' => $therapist->attendanceRecords()->absent()->count(),
                'total_late' => $therapist->attendanceRecords()->late()->count(),
            ],
            'ratings' => [
                'average' => $therapist->average_rating,
                'total' => $therapist->ratings()->count(),
                'distribution' => TherapistRating::getRatingDistribution($id),
            ],
            'commission' => CommissionRecord::getCommissionSummary($id, now()->startOfMonth(), now()->endOfMonth()),
            'performance' => PerformanceMetric::getMetricsSummary($id, $period),
            'availability' => [
                'upcoming_slots' => $therapist->getUpcomingAvailability(7)->count(),
                'total_slots' => $therapist->availabilitySlots()->count(),
                'available_slots' => $therapist->availabilitySlots()->available()->count(),
            ],
            'leave' => [
                'pending_requests' => $therapist->leaveRequests()->pending()->count(),
                'approved_requests' => $therapist->leaveRequests()->approved()->count(),
                'total_leave_days' => $therapist->leaveRequests()->approved()->sum('duration'),
            ],
        ];

        return response()->json($stats);
    }

    /**
     * Update therapist status
     */
    public function updateStatus(Request $request, $id)
    {
        $therapist = Therapist::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:' . implode(',', array_keys(Therapist::getStatuses())),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        switch ($request->status) {
            case 'active':
                $therapist->activate();
                break;
            case 'inactive':
                $therapist->deactivate();
                break;
            case 'suspended':
                $therapist->suspend();
                break;
            case 'on_leave':
                $therapist->setOnLeave();
                break;
        }

        return response()->json([
            'message' => 'Therapist status updated successfully',
            'therapist' => $therapist->fresh(),
        ]);
    }

    /**
     * Verify therapist license
     */
    public function verifyLicense($id)
    {
        $therapist = Therapist::findOrFail($id);
        $therapist->verifyLicense();

        return response()->json([
            'message' => 'License verified successfully',
            'therapist' => $therapist->fresh(),
        ]);
    }

    /**
     * Get available therapists for booking
     */
    public function getAvailableTherapists(Request $request)
    {
        $query = Therapist::active()
            ->acceptingNewClients()
            ->verified()
            ->with(['user', 'specializations', 'skills']);

        // Filter by specialization
        if ($request->has('specialization_id')) {
            $query->bySpecialization($request->specialization_id);
        }

        // Filter by date and time
        if ($request->has('date') && $request->has('time')) {
            $dateTime = \Carbon\Carbon::parse($request->date . ' ' . $request->time);
            $therapistIds = AvailabilitySlot::where('date', $request->date)
                ->where('start_time', '<=', $request->time)
                ->where('end_time', '>', $request->time)
                ->where('is_available', true)
                ->pluck('therapist_id');

            $query->whereIn('id', $therapistIds);
        }

        $therapists = $query->orderBy('average_rating', 'desc')->get();

        return response()->json([
            'therapists' => $therapists->map(function ($therapist) {
                return $therapist->getProfileSummary();
            }),
        ]);
    }
}
