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
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DeliveryController;

// Public routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public tracking routes
Route::get('/tracking', [TrackingController::class, 'searchForm'])->name('tracking.search');
Route::get('/tracking/{order}', [TrackingController::class, 'show'])->name('tracking.show');
Route::post('/tracking/search', [TrackingController::class, 'search'])->name('tracking.search.post');
Route::get('/tracking/{order}/updates', [TrackingController::class, 'getTrackingUpdates'])->name('tracking.updates');

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
    Route::get('/jobs/debug/list', [JobController::class, 'debugJobs'])->name('jobs.debug');
    Route::get('/jobs/qr/{qrCode}/details', [JobController::class, 'getJobDetailsByQr'])->name('jobs.qr.details');

    // Design routes (Added)
    Route::resource('designs', DesignController::class);
    Route::get('/designs/create/{order}', [DesignController::class, 'create'])->name('designs.create');
    Route::post('/designs/{order}', [DesignController::class, 'store'])->name('designs.store');
    Route::post('/designs/{design}/approve', [DesignController::class, 'approve'])->name('designs.approve');
    Route::post('/designs/{design}/reject', [DesignController::class, 'reject'])->name('designs.reject');

    // Design Template routes
    Route::resource('design-templates', DesignTemplateController::class);

    // Product routes
    Route::resource('products', ProductController::class);
    Route::post('/products/{product}/stock', [ProductController::class, 'updateStock'])->name('products.stock.update');
    Route::get('/products/for-order', [ProductController::class, 'getProductsForOrder'])->name('products.for-order');
    Route::get('/products/{product}/details', [ProductController::class, 'getProductDetails'])->name('products.details');

    // User Management routes (SuperAdmin only)
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('/users/stats', [UserController::class, 'getStats'])->name('users.stats');
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');

    // Reports routes (SuperAdmin only)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/orders', [ReportController::class, 'orderReport'])->name('reports.orders');
    Route::get('/reports/production', [ReportController::class, 'productionReport'])->name('reports.production');
    Route::get('/reports/users', [ReportController::class, 'userReport'])->name('reports.users');
    Route::get('/reports/financial', [ReportController::class, 'financialReport'])->name('reports.financial');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    Route::get('/orders/{order}/status', [OrderController::class, 'getStatus'])->name('orders.status');
    Route::get('/orders/{order}/tracking', [OrderController::class, 'tracking'])->name('orders.tracking');

// Offline Support Routes
Route::prefix('offline')->middleware(['auth'])->group(function () {
    Route::get('/jobs', [OfflineController::class, 'getOfflineJobs'])->name('offline.jobs');
    Route::post('/log-action', [OfflineController::class, 'logOfflineAction'])->name('offline.log-action');
    Route::post('/sync-logs', [OfflineController::class, 'syncOfflineLogs'])->name('offline.sync-logs');
    Route::get('/unsynced-logs', [OfflineController::class, 'getUnsyncedLogs'])->name('offline.unsynced-logs');
    Route::get('/check-status', [OfflineController::class, 'checkOnlineStatus'])->name('offline.check-status');
});

// Delivery Management routes (SuperAdmin, Admin, Sales Manager only)
Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
Route::get('/deliveries/{order}', [DeliveryController::class, 'show'])->name('deliveries.show');
Route::post('/deliveries/{order}/update-delivery', [DeliveryController::class, 'updateDeliveryStatus'])->name('deliveries.update-delivery');
Route::post('/deliveries/{order}/upload-proof', [DeliveryController::class, 'uploadProof'])->name('deliveries.upload-proof');
Route::get('/deliveries/stats', [DeliveryController::class, 'getStats'])->name('deliveries.stats');
Route::get('/deliveries/export', [DeliveryController::class, 'export'])->name('deliveries.export');

// Order delivery and payment routes
Route::post('/orders/{order}/update-delivery', [OrderController::class, 'updateDelivery'])->name('orders.update-delivery');
Route::post('/orders/{order}/update-payment', [OrderController::class, 'updatePayment'])->name('orders.update-payment');
});
