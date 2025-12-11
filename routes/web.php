<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Engineering\WorkOrderEngineeringController;
use App\Http\Controllers\GeneralAffair\GeneralAffairController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

// routes/web.php

Route::middleware(['auth', 'verified'])->group(function () {

    // Route Dashboard sebagai Landing Page
    Route::get('/dashboard', function () {
        return view('landing');
    })->name('dashboard');

    // Route Engineering
    Route::get('/engineering', [WorkOrderEngineeringController::class, 'index'])
        ->name('engineering.index');

    // Route General Affair
    Route::get('/general-affair', [GeneralAffairController::class, 'index'])
        ->name('ga.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Engineering
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
    Route::put('/engineering/{id}/update-status', [WorkOrderEngineeringController::class, 'updateStatus'])->name('work-orders.updateStatus');

    Route::prefix('ga')->name('ga.')->group(function () {
        //index(tabel utama) -> route('ga.index')
        //URL: /general-affair
        Route::get('/', [GeneralAffairController::class, 'index'])->name('index');

        //store simpan data -> rote(ga.store)
        //URL: /general-affair/store
        Route::post('/store', [GeneralAffairController::class, 'store'])->name('store');

        //Update status (admin edit) -> route(ga.updateStatus)
        //URL: /general-affair/{id}/update-status
        Route::put('/{id}/update-status', [GeneralAffairController::class, 'updateStatus'])->name('update-status');

        //Export -> route('ga.export')
        //URL: /general-affair/export
        Route::get('/export', [GeneralAffairController::class, 'export'])->name('export');
    });
});



require __DIR__ . '/auth.php';
