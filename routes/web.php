<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Middleware\AdminMiddleware;

use Dedoc\Scramble\CacheableGenerator;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

/*
|--------------------------------------------------------------------------
| Web Routes - Admin Dashboard & Auth
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/docs', function () {
    return redirect('/docs/api');
});

Route::get('/docs/test', function (CacheableGenerator $generator) {
    $config = Scramble::getGeneratorConfig(Scramble::DEFAULT_API);
    $result = $generator->generate($config);

    return view('scramble::scalar', [
        'spec' => $result->spec(),
        'config' => $config,
        'result' => $result,
    ]);
})->middleware(['web', RestrictedDocsAccess::class]);

// Admin Guest Routes
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');

// Admin Authenticated Protected Routes
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');

    // Products CRUD
    Route::get('/admin/products', [AdminDashboardController::class, 'products'])->name('admin.products');
    Route::post('/admin/products', [AdminDashboardController::class, 'storeProduct'])->name('admin.products.store');
    Route::put('/admin/products/{id}', [AdminDashboardController::class, 'updateProduct'])->name('admin.products.update');
    Route::delete('/admin/products/{id}', [AdminDashboardController::class, 'destroyProduct'])->name('admin.products.destroy');

    // Categories CRUD
    Route::get('/admin/categories', [AdminDashboardController::class, 'categories'])->name('admin.categories');
    Route::post('/admin/categories', [AdminDashboardController::class, 'storeCategory'])->name('admin.categories.store');
    Route::put('/admin/categories/{id}', [AdminDashboardController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/admin/categories/{id}', [AdminDashboardController::class, 'destroyCategory'])->name('admin.categories.destroy');

    // Orders CRUD
    Route::get('/admin/orders', [AdminDashboardController::class, 'orders'])->name('admin.orders');
    Route::post('/admin/orders', [AdminDashboardController::class, 'storeOrder'])->name('admin.orders.store');
    Route::patch('/admin/orders/{id}/status', [AdminDashboardController::class, 'updateOrderStatus'])->name('admin.orders.status');
    Route::delete('/admin/orders/{id}', [AdminDashboardController::class, 'destroyOrder'])->name('admin.orders.destroy');

    // Users CRUD
    Route::get('/admin/users', [AdminDashboardController::class, 'users'])->name('admin.users');
    Route::post('/admin/users', [AdminDashboardController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/admin/users/{id}', [AdminDashboardController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [AdminDashboardController::class, 'destroyUser'])->name('admin.users.destroy');

    // Coupons CRUD
    Route::get('/admin/coupons', [AdminDashboardController::class, 'coupons'])->name('admin.coupons');
    Route::post('/admin/coupons', [AdminDashboardController::class, 'storeCoupon'])->name('admin.coupons.store');
    Route::put('/admin/coupons/{id}', [AdminDashboardController::class, 'updateCoupon'])->name('admin.coupons.update');
    Route::delete('/admin/coupons/{id}', [AdminDashboardController::class, 'destroyCoupon'])->name('admin.coupons.destroy');

    // Reviews CRUD
    Route::get('/admin/reviews', [AdminDashboardController::class, 'reviews'])->name('admin.reviews');
    Route::post('/admin/reviews', [AdminDashboardController::class, 'storeReview'])->name('admin.reviews.store');
    Route::put('/admin/reviews/{id}', [AdminDashboardController::class, 'updateReview'])->name('admin.reviews.update');
    Route::delete('/admin/reviews/{id}', [AdminDashboardController::class, 'destroyReview'])->name('admin.reviews.destroy');
});
