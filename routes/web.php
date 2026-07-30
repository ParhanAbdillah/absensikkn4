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
    } elseif ($user->isKoordinator() || $user->isSekretaris()) {
        return redirect()->route('koordinator.dashboard');
    }
    return redirect()->route('anggota.dashboard');
})->middleware(['auth'])->name('dashboard');

// DPL Routes
Route::middleware(['auth', 'role:dpl'])->prefix('dpl')->name('dpl.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Dpl\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/attendance/{attendance}/approve', [\App\Http\Controllers\Dpl\DashboardController::class, 'approve'])->name('attendance.approve');
});

// Koordinator & Sekretaris Routes
Route::middleware(['auth', 'role:koordinator,sekretaris,dpl'])->prefix('koordinator')->name('koordinator.')->group(function () {
    // Routes accessible by DPL, Koordinator, Sekretaris
    Route::get('/attendance/rekap', [\App\Http\Controllers\Koordinator\AttendanceController::class, 'rekap'])->name('attendance.rekap');
    Route::get('/attendance/rekap/print', [\App\Http\Controllers\Koordinator\AttendanceController::class, 'print'])->name('attendance.rekap.print');
    Route::get('/attendance/rekap/export-word', [\App\Http\Controllers\Koordinator\AttendanceController::class, 'exportWord'])->name('attendance.rekap.export-word');
    Route::get('/reports', [\App\Http\Controllers\Koordinator\ActivityReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{user}', [\App\Http\Controllers\Koordinator\ActivityReportController::class, 'show'])->name('reports.show');


    // Routes accessible by Koordinator and Sekretaris only
    Route::middleware(['role:koordinator,sekretaris'])->group(function () {
        Route::post('/attendance/manual', [\App\Http\Controllers\Koordinator\AttendanceController::class, 'manualStore'])->name('attendance.manual');
        Route::get('/dashboard', [\App\Http\Controllers\Koordinator\DashboardController::class, 'index'])->name('dashboard');
        Route::post('/schedules/{schedule}/send-reminder', [\App\Http\Controllers\Koordinator\DashboardController::class, 'sendReminder'])->name('schedules.send-reminder');
        Route::resource('schedules', \App\Http\Controllers\Koordinator\ScheduleController::class);

        // Leave Request Management
        Route::get('/leave', [\App\Http\Controllers\Koordinator\LeaveRequestController::class, 'index'])->name('leave.index');
        Route::post('/leave/{leaveRequest}/approve', [\App\Http\Controllers\Koordinator\LeaveRequestController::class, 'approve'])->name('leave.approve');
        Route::post('/leave/{leaveRequest}/reject', [\App\Http\Controllers\Koordinator\LeaveRequestController::class, 'reject'])->name('leave.reject');
    });

    // Routes accessible by Sekretaris only (Kelola Anggota & Titik Lokasi)
    Route::middleware(['role:sekretaris'])->group(function () {
        Route::resource('users', \App\Http\Controllers\Koordinator\UserController::class)->only(['index','store','update','destroy']);
        Route::resource('locations', \App\Http\Controllers\Koordinator\LocationController::class);
    });
});

// Anggota Routes
Route::middleware(['auth', 'role:anggota,koordinator,sekretaris,dpl'])->prefix('anggota')->name('anggota.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Anggota\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/panduan', function () {
        return view('anggota.panduan');
    })->name('panduan');
    
    // Face Registration Routes
    Route::get('/face/register', [\App\Http\Controllers\Anggota\FaceRegistrationController::class, 'index'])->name('face.register');
    Route::post('/face/register', [\App\Http\Controllers\Anggota\FaceRegistrationController::class, 'store'])->name('face.store');
    Route::delete('/face/register', [\App\Http\Controllers\Anggota\FaceRegistrationController::class, 'destroy'])->name('face.destroy');

    // Attendance Routes
    Route::get('/attendance', [\App\Http\Controllers\Anggota\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-location', [\App\Http\Controllers\Anggota\AttendanceController::class, 'checkLocation'])->name('attendance.check-location');
    Route::post('/attendance/store', [\App\Http\Controllers\Anggota\AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/history', [\App\Http\Controllers\Anggota\AttendanceController::class, 'history'])->name('attendance.history');

    // Leave Request Routes
    Route::get('/leave', [\App\Http\Controllers\Anggota\LeaveRequestController::class, 'index'])->name('leave.index');
    Route::get('/leave/create', [\App\Http\Controllers\Anggota\LeaveRequestController::class, 'create'])->name('leave.create');
    Route::post('/leave', [\App\Http\Controllers\Anggota\LeaveRequestController::class, 'store'])->name('leave.store');

    // Activity Report Routes
    Route::get('/reports', [\App\Http\Controllers\Anggota\ActivityReportController::class, 'index'])->name('reports.index');
    Route::post('/reports', [\App\Http\Controllers\Anggota\ActivityReportController::class, 'store'])->name('reports.store');
    Route::put('/reports/{report}', [\App\Http\Controllers\Anggota\ActivityReportController::class, 'update'])->name('reports.update');
    Route::delete('/reports/{report}', [\App\Http\Controllers\Anggota\ActivityReportController::class, 'destroy'])->name('reports.destroy');
    Route::post('/reports/{report}/upload-pic', [\App\Http\Controllers\Anggota\ActivityReportController::class, 'uploadPic'])->name('reports.upload-pic');
    Route::get('/reports/export', [\App\Http\Controllers\Anggota\ActivityReportController::class, 'export'])->name('reports.export');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Finance/Buku Kas Routes
Route::middleware(['auth', 'role:bendahara'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Bendahara\FinanceController::class, 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\Bendahara\FinanceController::class, 'store'])->name('store');
    Route::delete('/{transaction}', [\App\Http\Controllers\Bendahara\FinanceController::class, 'destroy'])->name('destroy');
    Route::get('/export', [\App\Http\Controllers\Bendahara\FinanceController::class, 'export'])->name('export');

    // Activities Financial Ledger Routes
    Route::prefix('activities')->name('activities.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Bendahara\ActivityFinancialReportController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Bendahara\ActivityFinancialReportController::class, 'store'])->name('store');
        Route::get('/{report}', [\App\Http\Controllers\Bendahara\ActivityFinancialReportController::class, 'show'])->name('show');
        Route::delete('/{report}', [\App\Http\Controllers\Bendahara\ActivityFinancialReportController::class, 'destroy'])->name('destroy');
        Route::post('/{report}/items', [\App\Http\Controllers\Bendahara\ActivityFinancialReportController::class, 'storeItem'])->name('items.store');
        Route::delete('/items/{item}', [\App\Http\Controllers\Bendahara\ActivityFinancialReportController::class, 'destroyItem'])->name('items.destroy');
        Route::get('/{report}/export', [\App\Http\Controllers\Bendahara\ActivityFinancialReportController::class, 'export'])->name('export');
    });
});

Route::get('/migrate-db', function () {
    if (auth()->check() && (auth()->user()->isKoordinator() || auth()->user()->isSekretaris() || auth()->user()->isBendahara())) {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            return 'Migration successful:<br><pre>' . \Illuminate\Support\Facades\Artisan::output() . '</pre>';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
    abort(403);
});

require __DIR__.'/auth.php';
