<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipApplicationController;
use App\Http\Controllers\MembershipCategoryController;
use App\Http\Controllers\MembershipLevelController;
use App\Http\Controllers\MembershipStatusController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/membership/apply', [MembershipApplicationController::class, 'create'])->name('membership.create');
Route::post('/membership/apply', [MembershipApplicationController::class, 'store'])->name('membership.store');
Route::get('/weather', [WeatherController::class, 'show'])->name('weather.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', [AuthController::class, 'showVerifyNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::patch('members/{member}/toggle-status', [MemberController::class, 'toggleStatus'])->name('members.toggle-status');
    Route::post('members/{member}/renew', [TransactionController::class, 'renew'])->name('members.renew');
    Route::get('members/{member}/print', [MemberController::class, 'print'])->name('members.print');
    Route::resource('members', MemberController::class);

    Route::get('membership-status', [MembershipStatusController::class, 'index'])->name('membership-status.index');
    Route::put('membership-status/{member}', [MembershipStatusController::class, 'update'])->name('membership-status.update');

    Route::resource('categories', MembershipCategoryController::class);

    Route::resource('levels', MembershipLevelController::class);

    Route::patch('transactions/{transaction}/status', [TransactionController::class, 'updateStatus'])->name('transactions.update-status');
    Route::get('transactions/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt');
    Route::resource('transactions', TransactionController::class)->only(['index', 'create', 'store', 'show']);

    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store']);
});
