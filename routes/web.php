<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TourPackageController;
use App\Http\Controllers\Admin\RentalUnitController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\TourReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Front\TourController;
use App\Http\Controllers\Front\TourBookingController;
use App\Http\Controllers\Front\BookingController as FrontBookingController;


// ================= ADMIN =================
Route::prefix('bw-admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin|staff'])
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('tour-packages', TourPackageController::class);
        Route::resource('rental-units', RentalUnitController::class);
        Route::resource('bookings', BookingController::class)->only(['index', 'show', 'update']);
        Route::resource('promos', PromoController::class);
        Route::resource('bank-accounts', BankAccountController::class)->except(['show']);
        Route::resource('tour-reviews', TourReviewController::class)->only(['index', 'update', 'destroy']);

        Route::get('settings/general', [SettingController::class, 'general'])->name('settings.general');
        Route::post('settings/general', [SettingController::class, 'saveGeneral']);
        Route::resource('categories', \App\Http\Controllers\Admin\TourCategoryController::class);
        Route::delete(
            'tour-packages/photo/{photo}',
            [TourPackageController::class, 'deletePhoto']
        )
            ->name('tour-packages.delete-photo');
    });


// ================= FRONTEND =================

// Homepage
Route::get('/', [TourController::class, 'index'])->name('home');

// Detail paket
Route::get('/paket/{tourPackage:slug}', [TourController::class, 'show'])
    ->name('tour.show');

// Booking submit
Route::post('/booking', [TourBookingController::class, 'store'])
    ->name('booking.store');

// Booking show (checkout page)
Route::get('/booking/{booking}', [FrontBookingController::class, 'show'])
    ->name('booking.show');


// Breeze dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';
