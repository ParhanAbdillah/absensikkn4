<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isDpl()) {
        return redirect()->route('dpl.dashboard');
    } elseif ($user->isKoordinator()) {
        return redirect()->route('koordinator.dashboard');
    }
    return redirect()->route('anggota.dashboard');
})->middleware(['auth'])->name('dashboard');

// DPL Routes
Route::middleware(['auth', 'role:dpl'])->prefix('dpl')->name('dpl.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Dpl\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/attendance/{attendance}/approve', [\App\Http\Controllers\Dpl\DashboardController::class, 'approve'])->name('attendance.approve');
});

// Koordinator Routes
Route::middleware(['auth', 'role:koordinator'])->prefix('koordinator')->name('koordinator.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Koordinator\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/schedules/{schedule}/send-reminder', [\App\Http\Controllers\Koordinator\DashboardController::class, 'sendReminder'])->name('schedules.send-reminder');
    Route::resource('locations', \App\Http\Controllers\Koordinator\LocationController::class);
    Route::resource('schedules', \App\Http\Controllers\Koordinator\ScheduleController::class);
});

// Anggota Routes
Route::middleware(['auth', 'role:anggota,koordinator'])->prefix('anggota')->name('anggota.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Anggota\DashboardController::class, 'index'])->name('dashboard');
    
    // Face Registration Routes
    Route::get('/face/register', [\App\Http\Controllers\Anggota\FaceRegistrationController::class, 'index'])->name('face.register');
    Route::post('/face/register', [\App\Http\Controllers\Anggota\FaceRegistrationController::class, 'store'])->name('face.store');
    Route::delete('/face/register', [\App\Http\Controllers\Anggota\FaceRegistrationController::class, 'destroy'])->name('face.destroy');

    // Attendance Routes
    Route::get('/attendance', [\App\Http\Controllers\Anggota\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-location', [\App\Http\Controllers\Anggota\AttendanceController::class, 'checkLocation'])->name('attendance.check-location');
    Route::post('/attendance/store', [\App\Http\Controllers\Anggota\AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/history', [\App\Http\Controllers\Anggota\AttendanceController::class, 'history'])->name('attendance.history');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
