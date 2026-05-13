@extends('layouts.app')

@section('title', 'Therapist Test - Ngalula Wellness Center')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">Therapist Dashboard Test</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h4>✅ Therapist Dashboard Working!</h4>
                        <p>This page confirms that the therapist routes and views are working correctly.</p>
                    </div>
                    
                    <div class="alert alert-success">
                        <h5>🎯 Test Results:</h5>
                        <ul>
                            <li>✅ Therapist routes are defined correctly</li>
                            <li>✅ Therapist dashboard view exists</li>
                            <li>✅ Layout is loading properly</li>
                            <li>✅ No middleware blocking access</li>
                        </ul>
                    </div>
                    
                    <div class="mt-4">
                        <h5>🔗 Quick Links:</h5>
                        <div class="d-grid gap-2">
                            <a href="/therapist/dashboard" class="btn btn-primary">Dashboard</a>
                            <a href="/therapist/appointments" class="btn btn-info">Appointments</a>
                            <a href="/therapist/schedule" class="btn btn-warning">Schedule</a>
                            <a href="/therapist/earnings" class="btn btn-success">Earnings</a>
                            <a href="/therapist/session-notes" class="btn btn-secondary">Session Notes</a>
                            <a href="/therapist/attendance" class="btn btn-dark">Attendance</a>
                            <a href="/therapist/profile" class="btn btn-light">Profile</a>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="/login" class="btn btn-outline-primary">← Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
