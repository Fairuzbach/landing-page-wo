<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Engineering\WorkOrderEngineeringController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- 1. ROUTE EXPORT (FIXED) ---
    // Harus ditaruh sebelum route yang mengandung {parameter} agar tidak tertukar
    Route::get('/work-orders/export', [WorkOrderEngineeringController::class, 'export'])
        ->name('work-orders.export');

    // --- 2. ROUTE MENU UTAMA (INDEX) ---
    Route::get('/engineering/work-orders', [WorkOrderEngineeringController::class, 'index'])
        ->name('engineering.wo.index');

    // --- 3. ROUTE CRUD (STORE & UPDATE) ---
    // PENTING: Tambahkan ini karena form di Blade Anda memanggil 'work-orders.store'
    Route::post('engineering/work-orders', [WorkOrderEngineeringController::class, 'store'])
        ->name('work-orders.store');

    Route::put('engineering/work-orders/{workOrder}', [WorkOrderEngineeringController::class, 'update'])
        ->name('work-orders.update');
});

require __DIR__ . '/auth.php';
