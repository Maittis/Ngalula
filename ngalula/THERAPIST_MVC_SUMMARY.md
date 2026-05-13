# Therapist Management Module - MVC Architecture Summary

## Overview
This document provides a comprehensive overview of the Model-View-Controller (MVC) architecture for the Therapist Management Module implemented in the Laravel application.

---

## 📁 Models (M)

### 1. Therapist Model
**File:** `app/Models/Therapist.php`
**Purpose:** Core therapist profile and management

**Key Features:**
- Fillable fields for all therapist attributes
- Relationships: user, skills, specializations, schedules, availability, attendance, etc.
- Scopes: active, byStatus, byEmploymentType, bySpecialization, byRating, verified, etc.
- Accessors: statusLabel, employmentTypeLabel, formattedHourlyRate, averageRating, etc.
- Helper methods: activate(), deactivate(), suspend(), verifyLicense(), addSkill(), addSpecialization(), etc.

**Relationships:**
```php
belongsTo(User::class)
belongsToMany(Skill::class)
belongsToMany(Specialization::class)
hasMany(WorkingSchedule::class)
hasMany(AvailabilitySlot::class)
hasMany(AttendanceRecord::class)
hasMany(LeaveRequest::class)
hasMany(TherapistRating::class)
hasMany(PerformanceMetric::class)
hasMany(CommissionRecord::class)
hasMany(TherapistNote::class)
```

### 2. Skill Model
**File:** `app/Models/Skill.php`
**Purpose:** Skills and competencies management

**Key Features:**
- Skill categories and proficiency levels
- Many-to-many relationship with therapists
- Scopes: active, byCategory
- Helper methods: activate(), deactivate(), getTherapistsByLevel()

### 3. Specialization Model
**File:** `app/Models/Specialization.php`
**Purpose:** Therapist specializations and expertise areas

**Key Features:**
- Specialization categories and requirements
- Minimum experience and certification requirements
- Scopes: active, byCategory
- Helper methods: activate(), deactivate(), getTherapistCount()

### 4. WorkingSchedule Model
**File:** `app/Models/WorkingSchedule.php`
**Purpose:** Weekly work schedule management

**Key Features:**
- Day-of-week scheduling with time ranges
- Break times and maximum appointments
- Scopes: active, byDay, byTherapist
- Helper methods: isWithinWorkingHours(), getAvailableSlots()

### 5. AvailabilitySlot Model
**File:** `app/Models/AvailabilitySlot.php`
**Purpose:** Daily availability and appointment slots

**Key Features:**
- Date-specific availability with time ranges
- Conflict detection and booking management
- Recurring patterns support
- Scopes: available, booked, byDate, betweenDates
- Helper methods: book(), cancelBooking(), isOverlapping()

### 6. AttendanceRecord Model
**File:** `app/Models/AttendanceRecord.php`
**Purpose:** Attendance tracking and time management

**Key Features:**
- Check-in/check-out with break times
- Status tracking (present, absent, late)
- Overtime calculation and approval workflow
- Scopes: byStatus, present, absent, late, byDate
- Helper methods: checkIn(), checkOut(), calculateOvertime()

### 7. LeaveRequest Model
**File:** `app/Models/LeaveRequest.php`
**Purpose:** Leave management and approval system

**Key Features:**
- Multiple leave types (sick, vacation, personal, etc.)
- Approval/rejection workflow with comments
- Attachment support and coverage arrangement
- Scopes: byStatus, pending, approved, rejected
- Helper methods: approve(), reject(), isActive()

### 8. TherapistRating Model
**File:** `app/Models/TherapistRating.php`
**Purpose:** Client rating and feedback system

**Key Features:**
- 5-star rating system with comments
- Anonymous rating option
- Response functionality for therapists
- Scopes: byRating, verified, withComment
- Helper methods: verify(), respond(), getRatingCategory()

### 9. PerformanceMetric Model
**File:** `app/Models/PerformanceMetric.php`
**Purpose:** Performance tracking and benchmarking

**Key Features:**
- Multiple metric types (client satisfaction, productivity, etc.)
- Target setting and achievement tracking
- Period-based reporting (daily, weekly, monthly, etc.)
- Scopes: byType, byPeriod, aboveTarget, belowTarget
- Helper methods: calculatePercentageChange(), updateAchievedValue()

### 10. CommissionRecord Model
**File:** `app/Models/CommissionRecord.php`
**Purpose:** Commission calculation and payment tracking

**Key Features:**
- Multiple commission types (session, product sales, bonuses, etc.)
- Tax calculation and payment tracking
- Status management (pending, approved, paid, cancelled)
- Scopes: byStatus, byType, betweenDates, unpaid
- Helper methods: approve(), markAsPaid(), calculateTax()

### 11. TherapistNote Model
**File:** `app/Models/TherapistNote.php`
**Purpose:** Internal notes and documentation

**Key Features:**
- Multiple note types (performance, attendance, disciplinary, etc.)
- Privacy controls and importance flags
- Follow-up management with resolution tracking
- Scopes: byType, private, important, requiresFollowUp
- Helper methods: resolve(), requireFollowUp(), addTag()

---

## 🎮 Controllers (C)

### TherapistController
**File:** `app/Http/Controllers/TherapistController.php`
**Purpose:** Complete therapist management API

**Main Methods:**

#### CRUD Operations
- `index()` - List therapists with filtering and pagination
- `show($id)` - Get detailed therapist information
- `store(Request $request)` - Create new therapist
- `update(Request $request, $id)` - Update therapist
- `destroy($id)` - Delete therapist

#### Schedule Management
- `getWorkingSchedules($id)` - Get therapist schedules
- `createWorkingSchedule(Request $request, $id)` - Create schedule
- `getAvailabilitySlots(Request $request, $id)` - Get availability
- `createAvailabilitySlot(Request $request, $id)` - Create slot

#### Attendance Management
- `getAttendanceRecords(Request $request, $id)` - Get attendance
- `recordAttendance(Request $request, $id)` - Record attendance

#### Leave Management
- `getLeaveRequests(Request $request, $id)` - Get leave requests
- `createLeaveRequest(Request $request, $id)` - Create leave request

#### Rating System
- `getRatings(Request $request, $id)` - Get therapist ratings
- `addRating(Request $request, $id)` - Add rating

#### Performance & Commission
- `getPerformanceMetrics(Request $request, $id)` - Get metrics
- `addPerformanceMetric(Request $request, $id)` - Add metric
- `getCommissionRecords(Request $request, $id)` - Get commissions
- `addCommissionRecord(Request $request, $id)` - Add commission

#### Notes & Documentation
- `getNotes(Request $request, $id)` - Get notes
- `addNote(Request $request, $id)` - Add note

#### Statistics & Reporting
- `getStatistics(Request $request, $id)` - Get comprehensive stats

#### Utility Methods
- `updateStatus(Request $request, $id)` - Update therapist status
- `verifyLicense($id)` - Verify therapist license
- `getAvailableTherapists(Request $request)` - Find available therapists

---

## 🛣️ Routes (R - API Routes)

### File: `routes/api.php`

#### Therapist Management Routes Group
**Prefix:** `/api/therapists`
**Middleware:** `auth:sanctum`

##### Public Routes
```php
GET /therapists/available - Get available therapists for booking
```

##### Admin/Management Routes
**Middleware:** `role:admin,super_admin,receptionist`

```php
// CRUD Operations
GET /therapists - List therapists
POST /therapists - Create therapist
GET /therapists/{id} - Get therapist details
PUT /therapists/{id} - Update therapist
DELETE /therapists/{id} - Delete therapist

// Status Management
PUT /therapists/{id}/status - Update status
POST /therapists/{id}/verify-license - Verify license

// Schedule Management
GET /therapists/{id}/working-schedules - Get schedules
POST /therapists/{id}/working-schedules - Create schedule
GET /therapists/{id}/availability-slots - Get availability
POST /therapists/{id}/availability-slots - Create slot

// Attendance Management
GET /therapists/{id}/attendance - Get attendance
POST /therapists/{id}/attendance - Record attendance

// Leave Management
GET /therapists/{id}/leave-requests - Get leave requests
POST /therapists/{id}/leave-requests - Create leave request

// Rating System
GET /therapists/{id}/ratings - Get ratings
POST /therapists/{id}/ratings - Add rating

// Performance & Commission
GET /therapists/{id}/performance-metrics - Get metrics
POST /therapists/{id}/performance-metrics - Add metric
GET /therapists/{id}/commissions - Get commissions
POST /therapists/{id}/commissions - Add commission

// Notes & Documentation
GET /therapists/{id}/notes - Get notes
POST /therapists/{id}/notes - Add note

// Statistics
GET /therapists/{id}/statistics - Get statistics
```

##### Therapist-Specific Routes
**Middleware:** `role:therapist`

```php
// Profile Management
GET /therapists/me - Get my profile
PUT /therapists/me - Update my profile

// Schedule & Availability
GET /therapists/me/working-schedules - Get my schedules
GET /therapists/me/availability - Get my availability

// Attendance
GET /therapists/me/attendance - Get my attendance
POST /therapists/me/check-in - Check in
POST /therapists/me/check-out - Check out

// Leave Management
GET /therapists/me/leave-requests - Get my leave requests
POST /therapists/me/leave-requests - Create leave request

// Ratings & Performance
GET /therapists/me/ratings - Get my ratings
GET /therapists/me/commissions - Get my commissions
GET /therapists/me/statistics - Get my statistics
```

---

## 🗄️ Database Migrations

### Core Tables
1. **therapists** - Main therapist profiles
2. **skills** - Skills and competencies
3. **specializations** - Specialization categories
4. **therapist_skills** - Many-to-many skills relationship
5. **therapist_specializations** - Many-to-many specializations relationship

### Management Tables
6. **working_schedules** - Weekly work schedules
7. **availability_slots** - Daily availability slots
8. **attendance_records** - Attendance tracking
9. **leave_requests** - Leave management

### Performance & Finance Tables
10. **therapist_ratings** - Client ratings
11. **performance_metrics** - Performance tracking
12. **commission_records** - Commission management
13. **therapist_notes** - Internal notes

---

## 🔗 Updated User Model

### New Relationships Added to `app/Models/User.php`

```php
// Therapist Relationships
public function therapist()
public function therapistRatings() // as client
public function therapistNotes() // as author
public function approvedAttendanceRecords()
public function approvedLeaveRequests()
public function rejectedLeaveRequests()
public function paidCommissions()
public function resolvedTherapistNotes()
public function createdPerformanceMetrics()
public function respondedTherapistRatings()

// Helper Methods
public function getTherapistProfile()
public function hasTherapistProfile()
public function canManageTherapists()
public function canViewTherapistDetails()
public function canRateTherapists()
public function getTherapistRating($therapistId)
public function hasRatedTherapist($therapistId)

// Scopes
public function scopeTherapists($query)
public function scopeWithTherapistProfile($query)
public function scopeActiveTherapists($query)
public function scopeAvailableTherapists($query)
```

---

## 🎯 Key Features Summary

### Core Functionality
- ✅ Complete therapist profile management
- ✅ Skills and specializations tracking
- ✅ Working schedules and availability management
- ✅ Attendance tracking with approval workflows
- ✅ Leave request management system
- ✅ Client rating and feedback system
- ✅ Performance metrics tracking
- ✅ Commission calculation and payment
- ✅ Internal notes and documentation

### Advanced Features
- ✅ Role-based access control (RBAC)
- ✅ Conflict detection for availability slots
- ✅ Automated commission calculations
- ✅ Performance benchmarking and targets
- ✅ Multi-language support
- ✅ File attachment support
- ✅ Anonymous rating options
- ✅ Follow-up management system
- ✅ Statistical reporting and summaries

### API Capabilities
- ✅ Full CRUD operations for all entities
- ✅ Advanced filtering and search capabilities
- ✅ Pagination support
- ✅ File upload handling
- ✅ Comprehensive validation
- ✅ Error handling and response formatting
- ✅ RESTful API design
- ✅ Role-based endpoint protection

---

## 📊 Architecture Highlights

### Design Patterns Used
- **Repository Pattern** (implied through model methods)
- **Factory Pattern** (Laravel factories)
- **Observer Pattern** (Laravel events)
- **Strategy Pattern** (different commission types)
- **Template Method** (common model functionality)

### SOLID Principles
- **Single Responsibility** - Each model has one clear purpose
- **Open/Closed** - Models are extensible through relationships
- **Liskov Substitution** - Proper inheritance and interfaces
- **Interface Segregation** - Focused model methods
- **Dependency Inversion** - Dependency injection in controllers

### Security Features
- ✅ Input validation and sanitization
- ✅ Role-based access control
- ✅ File upload security
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ XSS protection (Laravel built-in)

---

## 🚀 Next Steps

The Therapist Management Module is now fully implemented with:
- Complete MVC architecture
- Comprehensive API endpoints
- Role-based access control
- Advanced features and functionality
- Modern Laravel best practices

Ready for integration with:
- Appointment booking system
- Payment processing
- Notification system
- Reporting dashboard
- Mobile application

---

*This document serves as a comprehensive reference for the Therapist Management Module implementation.*
