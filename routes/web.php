<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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
use App\Http\Controllers\Admin\ShipPackageController;
use App\Http\Controllers\Front\ShipController;
use App\Http\Controllers\Front\ShipOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\AffiliateUserController;

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
Route::resource('umrah-packages', \App\Http\Controllers\Admin\UmrahPackageController::class);
Route::delete('umrah-packages/photo/{photo}', [\App\Http\Controllers\Admin\UmrahPackageController::class, 'deletePhoto'])
    ->name('umrah-packages.delete-photo');
Route::get('categories/{category}/subcategories', [\App\Http\Controllers\Admin\TourCategoryController::class, 'subcategories'])
    ->name('categories.subcategories');
    // MICE
Route::resource('mice-packages', \App\Http\Controllers\Admin\MicePackageController::class);
Route::delete('mice-packages/photo/{photo}', [\App\Http\Controllers\Admin\MicePackageController::class, 'deletePhoto'])
    ->name('mice-packages.delete-photo');

Route::resource('mice-categories', \App\Http\Controllers\Admin\MiceCategoryController::class);
Route::prefix('affiliate')->name('affiliate.')->group(function () {
    Route::get('/requests', [\App\Http\Controllers\Admin\AffiliateApprovalController::class, 'index'])
        ->name('requests.index');
    Route::get('/requests/{user}', [\App\Http\Controllers\Admin\AffiliateApprovalController::class, 'show'])
        ->name('requests.show');
        Route::get('/orders', [\App\Http\Controllers\Admin\AffiliateOrderController::class, 'index'])
    ->name('orders.index');
Route::get('/orders/{order}', [\App\Http\Controllers\Admin\AffiliateOrderController::class, 'show'])
    ->name('orders.show');
    Route::get('/withdrawals', [\App\Http\Controllers\Admin\AffiliateWithdrawalController::class, 'index'])
    ->name('withdrawals.index');
Route::get('/withdrawals/{requestModel}', [\App\Http\Controllers\Admin\AffiliateWithdrawalController::class, 'show'])
    ->name('withdrawals.show');
Route::post('/withdrawals/{requestModel}/status', [\App\Http\Controllers\Admin\AffiliateWithdrawalController::class, 'updateStatus'])
    ->name('withdrawals.status');

Route::post('/orders/{order}/commission', [\App\Http\Controllers\Admin\AffiliateOrderController::class, 'setCommission'])
    ->name('orders.commission');



    Route::post('/requests/{user}/approve', [\App\Http\Controllers\Admin\AffiliateApprovalController::class, 'approve'])
        ->name('requests.approve');
    Route::post('/requests/{user}/decline', [\App\Http\Controllers\Admin\AffiliateApprovalController::class, 'decline'])
        ->name('requests.decline');
});


Route::resource('umrah-categories', \App\Http\Controllers\Admin\UmrahCategoryController::class);
        Route::post('legal-pages', [\App\Http\Controllers\Admin\LegalPagesController::class, 'update'])
            ->name('legal-pages.update');
Route::get('/reviews/packages', [AdminReviewController::class, 'packages'])
    ->name('reviews.packages');

Route::get('users/affiliate', [AffiliateUserController::class, 'index'])
    ->name('users.affiliate.index');

Route::post('users/affiliate/{user}', [AffiliateUserController::class, 'update'])
    ->name('users.affiliate.update');

    Route::resource('users', AdminUserController::class)->except(['create', 'store']);
        // Rent Car Package CRUD
        Route::resource('rent-car-packages', RentCarPackageController::class);
        Route::post('system/clear-cache', [SystemController::class, 'clearCache'])
            ->name('system.clear-cache');
            Route::resource('ship-packages', ShipPackageController::class);

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
Route::resource('ship-categories', \App\Http\Controllers\Admin\ShipCategoryController::class);

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
| User Panel
|--------------------------------------------------------------------------
*/
Route::prefix('user')
    ->name('user.')
    ->middleware(['auth', 'role:user', 'verified'])
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\User\DashboardController::class, 'index'])
            ->name('dashboard');
Route::get('/orders/{order}/confirm-admin', [\App\Http\Controllers\User\OrderController::class, 'confirmAdmin'])
    ->name('orders.confirm-admin');
Route::get('/withdrawals', [\App\Http\Controllers\User\AffiliateController::class, 'withdrawals'])
    ->name('withdrawals');

Route::post('/withdrawals', [\App\Http\Controllers\User\AffiliateController::class, 'submitWithdrawal'])
    ->name('withdrawals.submit');

        Route::prefix('affiliate')
    ->name('affiliate.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\User\AffiliateController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/commission', [\App\Http\Controllers\User\AffiliateController::class, 'commission'])
            ->name('commission');

       Route::get('/links', [\App\Http\Controllers\User\AffiliateController::class, 'links'])
    ->name('links');

Route::get('/links/create', [\App\Http\Controllers\User\AffiliateController::class, 'createLinkForm'])
    ->name('links.create');

Route::post('/links', [\App\Http\Controllers\User\AffiliateController::class, 'storeLink'])
    ->name('links.store');
Route::get('/apply', [\App\Http\Controllers\User\AffiliateController::class, 'apply'])
    ->name('apply');

Route::post('/apply', [\App\Http\Controllers\User\AffiliateController::class, 'submitApplication'])
    ->name('apply.submit');

        Route::get('/coupons', [\App\Http\Controllers\User\AffiliateController::class, 'coupons'])
    ->name('coupons');

Route::post('/coupons', [\App\Http\Controllers\User\AffiliateController::class, 'storeUserCoupon'])
    ->name('coupons.store');


        Route::get('/orders', [\App\Http\Controllers\User\AffiliateController::class, 'orders'])
            ->name('orders');
    });


        Route::get('/orders', [\App\Http\Controllers\User\OrderController::class, 'index'])
            ->name('orders');
            Route::get('/orders/{order}', [\App\Http\Controllers\User\OrderController::class, 'show'])
    ->name('orders.show');

        Route::get('/profile', [\App\Http\Controllers\User\ProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::post('/profile', [\App\Http\Controllers\User\ProfileController::class, 'update'])
            ->name('profile.update');
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
Route::get('/paket-mice', [\App\Http\Controllers\Front\MiceController::class, 'index'])->name('mice.index');
Route::get('/paket-mice/{micePackage:slug}', [\App\Http\Controllers\Front\MiceController::class, 'show'])->name('mice.show');
Route::post('/mice/{slug}/draft-booking', [\App\Http\Controllers\Front\MiceOrderController::class, 'draft'])->name('mice.draft');
Route::post('/rent-car/{slug}/draft-booking', [RentCarOrderController::class, 'draft'])
    ->name('rentcar.draft');

// Ship (Sewa Kapal)
Route::prefix('sewa-kapal')->name('ship.')->group(function () {
    Route::get('/', [ShipController::class, 'index'])->name('index');
    Route::get('/{slug}', [ShipController::class, 'show'])->name('show');
});

// Draft booking ship
Route::post('/sewa-kapal/{slug}/draft-booking', [ShipOrderController::class, 'draft'])
    ->name('ship.draft');

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
Route::get('/paket-umrah', [\App\Http\Controllers\Front\UmrahController::class, 'index'])
    ->name('umrah.index');

Route::get('/umrah/{umrahPackage:slug}', [\App\Http\Controllers\Front\UmrahController::class, 'show'])
    ->name('umrah.show');

Route::post('/umrah/{slug}/draft-booking', [\App\Http\Controllers\Front\UmrahOrderController::class, 'draft']);

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

Route::get('/dokumentasi', [FrontDocumentationController::class, 'tour'])->name('docs');
Route::get('/dokumentasi/sewa-kapal', [FrontDocumentationController::class, 'ship'])->name('docs.ship');
Route::get('/dokumentasi/umrah', [FrontDocumentationController::class, 'umrah'])->name('docs.umrah');
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
    // Jangan redirect untuk path API (biar gak 302)
    if (request()->is('api/*')) {
        abort(404);
    }

    if (request()->is('bw-admin/*')) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
});

Route::post('/logout-to-login', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout.to.login');