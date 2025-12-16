<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TourPackageController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\TourReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\RentCarPackageController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Front\PaymentController as FrontPaymentController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\DocumentationController as AdminDocumentationController;
use App\Http\Controllers\Admin\ArticleController;
/*
|--------------------------------------------------------------------------
| Front Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Front\TourController;
use App\Http\Controllers\Front\TourOrderController;        // <— DRAFT BOOKING TOUR
use App\Http\Controllers\Front\RentCarController;
use App\Http\Controllers\Front\RentCarOrderController;   // <— DRAFT BOOKING RENTCAR
use App\Http\Controllers\Front\ReviewController;
use App\Http\Controllers\Front\BookingController as FrontBookingController;
use App\Http\Controllers\Front\CheckoutController;
// Promo validator (frontend)
use App\Http\Controllers\PromoValidatorController;
use App\Http\Controllers\Front\DocumentationController as FrontDocumentationController;
use App\Http\Controllers\Front\ArticleController as FrontArticleController;



/*
|--------------------------------------------------------------------------
| Admin Panel
|--------------------------------------------------------------------------
*/

Route::prefix('bw-admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])

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
        Route::post('/payments/gateway/{gateway}', [PaymentController::class, 'toggleGateway'])
            ->name('payments.toggleGateway');


        // Promo Admin
        Route::resource('promos', PromoController::class)->except(['show']);

        // Bank Account Admin
        Route::resource('bank-accounts', BankAccountController::class)->except(['show']);

        Route::resource('articles', ArticleController::class);

        // Settings
        Route::get('settings/general', [SettingController::class, 'general'])->name('settings.general');
        Route::post('settings/general', [SettingController::class, 'saveGeneral'])->name('settings.general.save');

        // Orders (sistem baru)
        Route::get('orders/approved', [AdminOrderController::class, 'approved'])
            ->name('orders.approved');

        Route::get('orders/rejected', [AdminOrderController::class, 'rejected'])
            ->name('orders.rejected');

        Route::resource('orders', AdminOrderController::class)
            ->only(['index', 'show', 'update', 'destroy']);

        // Categories
        Route::resource('categories', \App\Http\Controllers\Admin\TourCategoryController::class);


        Route::resource('documentations', AdminDocumentationController::class);
        //review
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
        Route::patch('/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.delete');
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
    ->name('checkout.show');

Route::post('/checkout/{order}', [CheckoutController::class, 'process'])
    ->name('checkout.process');

/*
|--------------------------------------------------------------------------
| Payment Pages (frontend)
|--------------------------------------------------------------------------
*/
Route::get('/payment/{order}', [FrontPaymentController::class, 'show'])
    ->name('payment.page');

Route::post('/payment/{order}/manual', [FrontPaymentController::class, 'submitManual'])
    ->name('payment.manual.submit');

Route::get('/payment/{order}/waiting', [FrontPaymentController::class, 'waiting'])
    ->name('payment.waiting');

Route::post('/payment/{order}/gateway', [FrontPaymentController::class, 'startGateway'])
    ->name('payment.gateway.start');

Route::get('/payment/{order}/manual', [FrontPaymentController::class, 'manualPage'])
    ->name('payment.manual.page');

Route::get('/payment/{order}/gateway', [FrontPaymentController::class, 'gatewayPage'])
    ->name('payment.gateway.page');

/*
|--------------------------------------------------------------------------
| Frontend Pages
|--------------------------------------------------------------------------
*/
Route::get('/artikel', [FrontArticleController::class, 'index'])
    ->name('articles');

Route::get('/artikel/{slug}', [FrontArticleController::class, 'show'])
    ->name('article.show');
Route::get('/', [TourController::class, 'home'])->name('home');
Route::get('/paket-tour', [TourController::class, 'index'])->name('tours.index');

Route::get('/dokumentasi', [FrontDocumentationController::class, 'index'])->name('docs');
Route::view('/about', 'front.pages.about')->name('about');

Route::post('/review', [ReviewController::class, 'store'])
    ->middleware('throttle:3,10')
    ->name('review.store');
// Homepage
Route::get('/', [TourController::class, 'home'])->name('home');
Route::get('/paket-tour', [TourController::class, 'index'])->name('tours.index');

// Tour detail
Route::get('/paket/{tourPackage:slug}', [TourController::class, 'show'])
    ->name('tour.show');

// Rent Car listing + detail
Route::prefix('rent-car')->name('rentcar.')->group(function () {

    Route::get('/', [RentCarController::class, 'index'])->name('index');

    Route::get('/{slug}', [RentCarController::class, 'show'])->name('show');
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
