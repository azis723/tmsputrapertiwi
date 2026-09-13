<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\AttendanceReportController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\MajorController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\ScheduleTimeController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\SchoolSettingController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\FaceRegistrationController;
use App\Http\Controllers\Student\HistoryController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\ScheduleController as StudentScheduleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Unified Portal & Specific Logins)
|--------------------------------------------------------------------------
*/
Route::get('/', [AuthController::class, 'showLogin'])->name('login');

// Admin Auth
Route::get('/admin/login', function () {
    return redirect()->route('login', ['role' => 'admin']);
})->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.process');
Route::post('/admin/logout', [AuthController::class, 'adminLogout'])->name('admin.logout');

// Student Auth
Route::get('/student/login', function () {
    return redirect()->route('login', ['role' => 'student']);
})->name('student.login');
Route::post('/student/login', [AuthController::class, 'studentLogin'])->name('student.login.process');
Route::post('/student/logout', [AuthController::class, 'studentLogout'])->name('student.logout');

/*
|--------------------------------------------------------------------------
| Administrator Routes (/admin/*)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:web', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users (Admin Accounts)
    Route::resource('users', UserController::class)->except(['show']);

    // Teachers
    Route::resource('teachers', TeacherController::class)->except(['show']);

    // Majors & Geofencing
    Route::resource('majors', MajorController::class)->except(['show']);

    // Academic Years
    Route::resource('academic-years', AcademicYearController::class)->except(['show']);
    Route::patch('academic-years/{academic_year}/set-active', [AcademicYearController::class, 'setActive'])->name('academic-years.set-active');

    // Classes
    Route::resource('classes', SchoolClassController::class)->except(['show']);

    // Students
    Route::resource('students', StudentController::class)->except(['show']);
    Route::post('students/{student}/reset-face', [StudentController::class, 'resetFace'])->name('students.reset-face');

    // Subjects
    Route::resource('subjects', SubjectController::class)->except(['show']);

    // Schedule Times
    Route::resource('schedule-times', ScheduleTimeController::class)->except(['show']);

    // Schedules
    Route::resource('schedules', ScheduleController::class)->except(['show']);

    // Holidays
    Route::resource('holidays', HolidayController::class)->except(['show']);

    // Attendance Reports & Exports
    Route::get('/attendances', [AttendanceReportController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/export/pdf', [AttendanceReportController::class, 'exportPdf'])->name('attendances.export.pdf');
    Route::get('/attendances/export/excel', [AttendanceReportController::class, 'exportExcel'])->name('attendances.export.excel');

    // School Settings
    Route::get('/settings', [SchoolSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SchoolSettingController::class, 'update'])->name('settings.update');

    // Blog & Categories
    Route::resource('posts', PostController::class)->except(['show']);
    Route::post('/categories', [PostController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/categories/{category}', [PostController::class, 'destroyCategory'])->name('categories.destroy');
});

/*
|--------------------------------------------------------------------------
| Student Portal Routes (/student/*)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:student', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    // Face Registration Biometrics
    Route::get('/face-registration', [FaceRegistrationController::class, 'index'])->name('face-registration');
    Route::post('/face-registration', [FaceRegistrationController::class, 'store'])->name('face-registration.store');

    // Self Attendance with Geofencing & Face Recognition
    Route::get('/attendance', [StudentAttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance', [StudentAttendanceController::class, 'store'])->name('attendance.store');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history');

    // Schedule
    Route::get('/schedule', [StudentScheduleController::class, 'index'])->name('schedule');

    // Profile & Password
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});
