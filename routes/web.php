<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// AUTH ROUTES
// ============================================================================
// Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Register
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Check auth status (API)
Route::get('/auth/check', [AuthController::class, 'check'])->name('auth.check');

// ============================================================================
// TWO FACTOR AUTHENTICATION ROUTES
// ============================================================================
Route::middleware(['auth'])->prefix('2fa')->name('2fa.')->group(function () {
    Route::get('/setup', [TwoFactorController::class, 'showSetup'])->name('setup');
    Route::post('/enable', [TwoFactorController::class, 'enableTwoFactor'])->name('enable');
    Route::get('/verify', [TwoFactorController::class, 'showVerify'])->name('verify');
    Route::post('/verify', [TwoFactorController::class, 'verify'])->name('verify.post');
    Route::post('/disable', [TwoFactorController::class, 'disable'])->name('disable');
});

// ============================================================================
// PUBLIC ROUTES
// ============================================================================
// Index page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Category pages
Route::get('/category/{id}', [CategoryController::class, 'show'])->name('category.show');

// Brand pages
Route::get('/brand/{id}', [BrandController::class, 'show'])->name('brand.show');

// Product-detail page
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

// Search page
Route::get('/search', [ProductController::class, 'search'])->name('pages.search');

// ============================================================================
// CHATBOT ROUTES (AI Product Search)
// ============================================================================
Route::post('/chatbot/search', [ChatbotController::class, 'search'])->name('chatbot.search');
Route::post('/chatbot/chat', [ChatbotController::class, 'chat'])->name('chatbot.chat');
Route::get('/chatbot/suggestions', [ChatbotController::class, 'suggestions'])->name('chatbot.suggestions');

// ============================================================================
// CART ROUTES (Require Authentication & 2FA)
// ============================================================================
Route::middleware(['auth', '2fa'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update/{rowId}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove/{rowId}', [CartController::class, 'destroy'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.applyCoupon');
    Route::post('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.removeCoupon');
    
    // Checkout routes
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/process', [CartController::class, 'processCheckout'])->name('checkout.process');
    
    // Order success
    Route::get('/order/success/{id}', [OrderController::class, 'success'])->name('order.success');
});

// ============================================================================
// ADMIN ROUTES (Require Authentication, 2FA & Admin Role)
// ============================================================================
Route::middleware(['admin', '2fa'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('index');
    
    // Products Management - CRUD
    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    
    // Categories Management - CRUD
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Brands Management - CRUD
    Route::get('/brands', [BrandController::class, 'index'])->name('brands');
    Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/{id}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/brands/{id}', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('/brands/{id}', [BrandController::class, 'destroy'])->name('brands.destroy');
    
    // Discounts Management - CRUD
    Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts');
    Route::post('/discounts', [DiscountController::class, 'store'])->name('discounts.store');
    Route::get('/discounts/{id}/edit', [DiscountController::class, 'edit'])->name('discounts.edit');
    Route::put('/discounts/{id}', [DiscountController::class, 'update'])->name('discounts.update');
    Route::delete('/discounts/{id}', [DiscountController::class, 'destroy'])->name('discounts.destroy');
    
    // Users Management - CRUD
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // Orders Management - CRUD
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
    
    // Analytics & Reports
    Route::get('/analytics', function () {
        return view('admin.analytics');
    })->name('analytics');
    
    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});
