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

// ADMIN PANEL
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
    });

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';
