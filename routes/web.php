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
use App\Http\Controllers\Admin\DestinationInspirationController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SystemController;
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
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        // Tour
        Route::resource('tour-packages', TourPackageController::class);
        Route::delete('tour-packages/photo/{photo}', [TourPackageController::class, 'deletePhoto'])
            ->name('tour-packages.delete-photo');
        Route::get('seo', [SeoController::class, 'edit'])->name('seo.edit');
        Route::post('seo', [SeoController::class, 'update'])->name('seo.update');
        Route::get('legal-pages', [\App\Http\Controllers\Admin\LegalPagesController::class, 'edit'])
            ->name('legal-pages.edit');

        Route::post('legal-pages', [\App\Http\Controllers\Admin\LegalPagesController::class, 'update'])
            ->name('legal-pages.update');

        // Rent Car Package CRUD
        Route::resource('rent-car-packages', RentCarPackageController::class);
        Route::post('system/clear-cache', [SystemController::class, 'clearCache'])
            ->name('system.clear-cache');
        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/bank', [PaymentController::class, 'addBank'])->name('bank.add');
        Route::delete('/payments/bank/{bank}', [PaymentController::class, 'deleteBank'])->name('bank.delete');
        Route::post('/payments/gateway/{gateway}', [PaymentController::class, 'toggleGateway'])
            ->name('payments.toggleGateway');
        Route::put('/payments/unique-code-setting', [PaymentController::class, 'updateUniqueCodeSetting'])
            ->name('payments.unique-code-setting');

        Route::resource('client-logos', \App\Http\Controllers\Admin\ClientLogoController::class);

        // Promo Admin
        Route::resource('promos', PromoController::class)->except(['show']);

        // Bank Account Admin
        Route::resource('bank-accounts', BankAccountController::class)->except(['show']);

        Route::resource('articles', ArticleController::class);
        Route::get('home-sections/promo-tours', [\App\Http\Controllers\Admin\HomePromoToursController::class, 'edit'])
            ->name('home-sections.promo-tours.edit');

        Route::post('home-sections/promo-tours', [\App\Http\Controllers\Admin\HomePromoToursController::class, 'update'])
            ->name('home-sections.promo-tours.update');
        // Settings
        Route::get('settings/general', [SettingController::class, 'general'])->name('settings.general');
        Route::post('settings/general', [SettingController::class, 'saveGeneral'])->name('settings.general.save');

        // Orders (sistem baru)
        Route::get('orders/approved', [AdminOrderController::class, 'approved'])
            ->name('orders.approved');

        Route::get('orders/rejected', [AdminOrderController::class, 'rejected'])
            ->name('orders.rejected');

        // ✅ taruh rekap dulu
        Route::get('orders/rekap', [AdminOrderController::class, 'rekap'])
            ->name('orders.rekap');

        Route::get('orders/rekap/print', [AdminOrderController::class, 'printRekap'])
            ->name('orders.rekap.print');

        // ✅ resource terakhir, hanya sekali
        Route::resource('orders', AdminOrderController::class)
            ->only(['index', 'show', 'update', 'destroy']);


        Route::get('/reviews/create', [AdminReviewController::class, 'create'])->name('reviews.create');
        Route::post('/reviews', [AdminReviewController::class, 'store'])->name('reviews.store');


        // Categories
        Route::resource('categories', \App\Http\Controllers\Admin\TourCategoryController::class);

        Route::resource('rent-car-categories', \App\Http\Controllers\Admin\RentCarCategoryController::class);
        Route::resource(
            'destination-inspirations',
            \App\Http\Controllers\Admin\DestinationInspirationController::class
        );

        Route::resource('documentations', AdminDocumentationController::class);
        //review
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
        Route::patch('/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.delete');
        Route::get('/reviews/{review}/edit', [AdminReviewController::class, 'edit'])->name('reviews.edit');
        Route::patch('/reviews/{review}', [AdminReviewController::class, 'update'])->name('reviews.update');
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


Route::get('/lang/{locale}', function ($locale) {
    $available = array_keys(config('app.available_locales', []));
    abort_unless(in_array($locale, $available, true), 404);

    session(['locale' => $locale]);
    return back();
})->name('lang.switch');


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
Route::get('/payment/{order}/gateway', [\App\Http\Controllers\Front\PaymentController::class, 'gatewayPage'])
    ->name('payment.gateway.page');

Route::post('/payment/{order}/gateway', [\App\Http\Controllers\Front\PaymentController::class, 'startGateway'])
    ->name('payment.gateway.start');

Route::get('/payment/{order}/paypal/return', [\App\Http\Controllers\Front\PaymentController::class, 'paypalReturn'])
    ->name('paypal.return');

Route::get('/payment/{order}/paypal/cancel', [\App\Http\Controllers\Front\PaymentController::class, 'paypalCancel'])
    ->name('paypal.cancel');



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
Route::get('/privacy-policy', [\App\Http\Controllers\Front\LegalController::class, 'privacy'])
    ->name('privacy-policy');

Route::get('/terms-conditions', [\App\Http\Controllers\Front\LegalController::class, 'terms'])
    ->name('terms-conditions');

Route::get('/contact', [\App\Http\Controllers\Front\LegalController::class, 'contact'])
    ->name('contact');

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
Route::fallback(function () {
    if (request()->is('bw-admin/*')) {
        // kalau admin salah URL, balikin ke dashboard admin
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
});
