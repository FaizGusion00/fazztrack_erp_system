<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\DesignTemplateController;
use App\Http\Controllers\OfflineController;

// Public routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public tracking routes
Route::get('/tracking', [TrackingController::class, 'searchForm'])->name('tracking.search');
Route::get('/tracking/{order}', [TrackingController::class, 'show'])->name('tracking.show');
Route::post('/tracking/search', [TrackingController::class, 'search'])->name('tracking.search.post');

// Health check route
Route::get('/api/health', function () {
    return response()->json(['status' => 'ok']);
});

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {
    
    // Dashboard routes
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/admin/dashboard', [AuthController::class, 'adminDashboard'])->name('admin.admin-dashboard');
    Route::get('/sales/dashboard', [AuthController::class, 'salesDashboard'])->name('sales.dashboard');
    Route::get('/designer/dashboard', [AuthController::class, 'designerDashboard'])->name('designer.dashboard');
    Route::get('/production/dashboard', [AuthController::class, 'productionDashboard'])->name('production.dashboard');
    
    // All authenticated routes with role-based access control in controllers
    Route::resource('clients', ClientController::class);
    Route::resource('orders', OrderController::class);
    
    // Job routes - specific routes first, then resource route
    Route::get('/jobs/scanner', [JobController::class, 'scanner'])->name('jobs.scanner');
    Route::get('/production/offline', function () {
        return view('production.offline');
    })->name('production.offline');
    Route::post('/jobs/scan', [JobController::class, 'scanQr'])->name('jobs.scan');
    Route::get('/jobs/users', [JobController::class, 'getAvailableUsers'])->name('jobs.users');
    Route::resource('jobs', JobController::class);
    
    // Additional routes
    Route::post('/clients/{client}/contacts', [ClientController::class, 'addContact'])->name('clients.contacts.add');
    Route::delete('/clients/{client}/contacts/{contact}', [ClientController::class, 'removeContact'])->name('clients.contacts.remove');
    Route::post('/orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
    Route::post('/orders/{order}/hold', [OrderController::class, 'putOnHold'])->name('orders.hold');
    Route::post('/orders/{order}/resume', [OrderController::class, 'resume'])->name('orders.resume');
    Route::post('/orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
    Route::post('/orders/{order}/jobs', [OrderController::class, 'createJobs'])->name('orders.jobs.create');
    Route::post('/jobs/{job}/start', [JobController::class, 'startJob'])->name('jobs.start');
    Route::post('/jobs/{job}/end', [JobController::class, 'endJob'])->name('jobs.end');
    Route::post('/jobs/{job}/assign', [JobController::class, 'assign'])->name('jobs.assign');
    Route::get('/jobs/{job}/assign', [JobController::class, 'assign'])->name('jobs.assign.get');
    Route::get('/jobs/{job}/qr', [JobController::class, 'generateQr'])->name('jobs.qr');
    Route::get('/jobs/{job}/print', [JobController::class, 'printJob'])->name('jobs.print');
    Route::get('/jobs/{job}/details', [JobController::class, 'getJobDetails'])->name('jobs.details');
    Route::get('/jobs/{job}/workflow', [JobController::class, 'getWorkflowInfo'])->name('jobs.workflow');

    // Design routes (Added)
    Route::resource('designs', DesignController::class);
    Route::get('/designs/create/{order}', [DesignController::class, 'create'])->name('designs.create');
    Route::post('/designs/{order}', [DesignController::class, 'store'])->name('designs.store');
    Route::post('/designs/{design}/approve', [DesignController::class, 'approve'])->name('designs.approve');
    Route::post('/designs/{design}/reject', [DesignController::class, 'reject'])->name('designs.reject');

    // Design Template routes
    Route::resource('design-templates', DesignTemplateController::class);

// Offline Support Routes
Route::prefix('offline')->middleware(['auth'])->group(function () {
    Route::get('/jobs', [OfflineController::class, 'getOfflineJobs'])->name('offline.jobs');
    Route::post('/log-action', [OfflineController::class, 'logOfflineAction'])->name('offline.log-action');
    Route::post('/sync-logs', [OfflineController::class, 'syncOfflineLogs'])->name('offline.sync-logs');
    Route::get('/unsynced-logs', [OfflineController::class, 'getUnsyncedLogs'])->name('offline.unsynced-logs');
    Route::get('/check-status', [OfflineController::class, 'checkOnlineStatus'])->name('offline.check-status');
});
});
