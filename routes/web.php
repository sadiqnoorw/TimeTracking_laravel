<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimeTrackingController;


Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [TimeTrackingController::class, 'index'])->name('dashboard');
    Route::post('/start-work', [TimeTrackingController::class, 'startWork'])->name('start.work');
    Route::post('/stop-work', [TimeTrackingController::class, 'stopWork'])->name('stop.work');
    Route::post('/start-break', [TimeTrackingController::class, 'startBreak'])->name('start.break');
    Route::post('/stop-break', [TimeTrackingController::class, 'stopBreak'])->name('stop.break');
    Route::get('/time-report', [TimeTrackingController::class, 'report'])->name('time.report');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
