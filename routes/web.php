<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route(auth()->user()->homeRoute())
        : redirect()->route('login');
});

// ---------- ยังไม่ล็อกอิน ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

// ---------- ล็อกอินแล้ว ----------
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // โปรไฟล์ของตัวเอง (ทุกบทบาท)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // ทุกบทบาท (ลูกค้าเห็นเฉพาะของตัวเอง ตรวจสิทธิ์ใน controller)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/payment-status', [OrderController::class, 'paymentStatus'])->name('orders.payment.status');

    // ผู้ดูแลระบบ + เจ้าของร้าน
    Route::middleware('role:admin,owner')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::resource('products', ProductController::class)->except('show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('/orders/{order}/shipment', [OrderController::class, 'createShipment'])->name('orders.shipment.create');
        Route::get('/orders/{order}/label', [OrderController::class, 'label'])->name('orders.label');
        Route::patch('/orders/{order}/payment', [OrderController::class, 'reviewPayment'])->name('orders.payment.review');
    });

    // ผู้ดูแลระบบเท่านั้น
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
    });

    // ลูกค้าเท่านั้น
    Route::middleware('role:customer')->group(function () {
        Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
        Route::get('/shop/{product}', [ShopController::class, 'show'])->name('shop.show');

        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
        Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');

        Route::post('/orders/{order}/payment', [OrderController::class, 'submitPayment'])->name('orders.payment.submit');

        Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
        Route::post('/checkout', [CartController::class, 'place'])->name('checkout.place');
    });
});
