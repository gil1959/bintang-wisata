<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TourPackageController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\TourReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\RentCarPackageController;

/*
|--------------------------------------------------------------------------
| Front Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Front\TourController;
use App\Http\Controllers\Front\TourOrderController;        // <— DRAFT BOOKING TOUR
use App\Http\Controllers\Front\RentCarController;
use App\Http\Controllers\Front\RentCarOrderController;   // <— DRAFT BOOKING RENTCAR

use App\Http\Controllers\Front\BookingController as FrontBookingController;
use App\Http\Controllers\Front\CheckoutController;
// Promo validator (frontend)
use App\Http\Controllers\PromoValidatorController;



/*
|--------------------------------------------------------------------------
| Admin Panel
|--------------------------------------------------------------------------
*/

Route::prefix('bw-admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin|staff'])
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Tour
        Route::resource('tour-packages', TourPackageController::class);
        Route::delete('tour-packages/photo/{photo}', [TourPackageController::class, 'deletePhoto'])
            ->name('tour-packages.delete-photo');

        // Rent Car Package CRUD
        Route::resource('rent-car-packages', RentCarPackageController::class);

        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/bank', [PaymentController::class, 'addBank'])->name('bank.add');
        Route::delete('/payments/bank/{bank}', [PaymentController::class, 'deleteBank'])->name('bank.delete');
        Route::post('/payments/gateway/{gateway}', [PaymentController::class, 'updateGateway'])
            ->name('gateway.update');

        // Booking Admin
        Route::resource('bookings', BookingController::class)->only(['index', 'show', 'update']);

        // Promo Admin
        Route::resource('promos', PromoController::class)->except(['show']);

        // Bank Account Admin
        Route::resource('bank-accounts', BankAccountController::class)->except(['show']);

        // Review
        Route::resource('tour-reviews', TourReviewController::class)->only(['index', 'update', 'destroy']);

        // Settings
        Route::get('settings/general', [SettingController::class, 'general'])->name('settings.general');
        Route::post('settings/general', [SettingController::class, 'saveGeneral']);

        // Categories
        Route::resource('categories', \App\Http\Controllers\Admin\TourCategoryController::class);
    });



/*
|--------------------------------------------------------------------------
| Promo Validation (Frontend)
|--------------------------------------------------------------------------
| HARUS bisa dipakai guest → JANGAN kasih middleware auth
*/
Route::post('/promo/validate', [PromoValidatorController::class, 'validatePromo'])
    ->name('promo.validate');



/*
|--------------------------------------------------------------------------
| NEW BOOKING SYSTEM (Modern Checkout)
|--------------------------------------------------------------------------
*/
// Rent Car Draft Booking
Route::post('/tours/{slug}/draft-booking', [TourOrderController::class, 'draft'])
    ->name('tour.draft');

Route::post('/rent-car/{slug}/draft-booking', [RentCarOrderController::class, 'draft'])
    ->name('rentcar.draft');

Route::get('/checkout/{order}', [CheckoutController::class, 'show'])
    ->name('checkout');


Route::get('/checkout/{order}', [CheckoutController::class, 'show'])
    ->name('checkout.show');

Route::post('/checkout/{order}', [CheckoutController::class, 'process'])
    ->name('checkout.process');
/*
|--------------------------------------------------------------------------
| Frontend Pages
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [TourController::class, 'index'])->name('home');

// Tour detail
Route::get('/paket/{tourPackage:slug}', [TourController::class, 'show'])
    ->name('tour.show');

// Rent Car listing + detail
Route::prefix('rent-car')->name('rentcar.')->group(function () {

    Route::get('/', [RentCarController::class, 'index'])->name('index');

    Route::get('/{slug}', [RentCarController::class, 'show'])->name('show');

    // OLD SYSTEM (bisa dimatikan nanti)
    Route::post('/{slug}/book', [RentCarController::class, 'postBooking'])
        ->name('book');
});






/*
|--------------------------------------------------------------------------
| Breeze Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';
