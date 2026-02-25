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
use App\Http\Controllers\Admin\PopupWidgetController;

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
use App\Http\Controllers\Front\PartnerRegistrationController;
use App\Http\Controllers\Partner\DashboardController as PartnerDashboardController;
use App\Http\Controllers\Admin\PartnerApplicationController;
use App\Http\Controllers\Admin\PartnerProductReviewController;
use App\Http\Controllers\Partner\ProfileController as PartnerProfileController;
use App\Http\Controllers\Partner\OrderController as PartnerOrderController;
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
use App\Http\Controllers\Admin\HomeSettingController;



/*
|--------------------------------------------------------------------------
| Admin Panel
|--------------------------------------------------------------------------
*/

Route::prefix('bw-admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin|site_moderator'])

    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:admin.dashboard.view');
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('permission:admin.dashboard.view');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update')->middleware('permission:admin.dashboard.view');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password')->middleware('permission:admin.dashboard.view');
        // Tabungan Umrah
        Route::get('/tabungan-umrah/accounts/pending', [\App\Http\Controllers\Admin\TabunganUmrahAdminController::class, 'pendingAccounts'])
            ->name('tabungan-umrah.accounts.pending')->middleware('permission:admin.dashboard.view');

        Route::get('/tabungan-umrah/accounts/verified', [\App\Http\Controllers\Admin\TabunganUmrahAdminController::class, 'verifiedAccounts'])
            ->name('tabungan-umrah.accounts.verified')->middleware('permission:admin.dashboard.view');

        Route::get('/tabungan-umrah/accounts/{account}', [\App\Http\Controllers\Admin\TabunganUmrahAdminController::class, 'showAccount'])
            ->name('tabungan-umrah.accounts.show')->middleware('permission:admin.dashboard.view');
        Route::get('/tabungan-umrah/accounts/{account}/statement/print', [\App\Http\Controllers\Admin\TabunganUmrahAdminController::class, 'printStatement'])
            ->name('tabungan-umrah.accounts.statement.print')->middleware('permission:admin.dashboard.view');

        Route::post('/tabungan-umrah/accounts/{account}/verify', [\App\Http\Controllers\Admin\TabunganUmrahAdminController::class, 'verifyAccount'])
            ->name('tabungan-umrah.accounts.verify')->middleware('permission:admin.dashboard.view');

        Route::post('/tabungan-umrah/accounts/{account}/reject', [\App\Http\Controllers\Admin\TabunganUmrahAdminController::class, 'rejectAccount'])
            ->name('tabungan-umrah.accounts.reject')->middleware('permission:admin.dashboard.view');

        Route::post('/tabungan-umrah/accounts/{account}/suspend', [\App\Http\Controllers\Admin\TabunganUmrahAdminController::class, 'suspendAccount'])
            ->name('tabungan-umrah.accounts.suspend')->middleware('permission:admin.dashboard.view');

        Route::post('/tabungan-umrah/accounts/{account}/unsuspend', [\App\Http\Controllers\Admin\TabunganUmrahAdminController::class, 'unsuspendAccount'])
            ->name('tabungan-umrah.accounts.unsuspend')->middleware('permission:admin.dashboard.view');

        Route::get('/tabungan-umrah/deposits', [\App\Http\Controllers\Admin\TabunganUmrahAdminController::class, 'depositsIndex'])
            ->name('tabungan-umrah.deposits.index')->middleware('permission:admin.dashboard.view');

        Route::get('/tabungan-umrah/deposits/{deposit}', [\App\Http\Controllers\Admin\TabunganUmrahAdminController::class, 'showDeposit'])
            ->name('tabungan-umrah.deposits.show')->middleware('permission:admin.dashboard.view');

        Route::post('/tabungan-umrah/deposits/{deposit}/approve', [\App\Http\Controllers\Admin\TabunganUmrahAdminController::class, 'approveDeposit'])
            ->name('tabungan-umrah.deposits.approve')->middleware('permission:admin.dashboard.view');

        Route::post('/tabungan-umrah/deposits/{deposit}/reject', [\App\Http\Controllers\Admin\TabunganUmrahAdminController::class, 'rejectDeposit'])
            ->name('tabungan-umrah.deposits.reject')->middleware('permission:admin.dashboard.view');
        Route::get('/tabungan-umrah/accounts/{account}/edit', [\App\Http\Controllers\Admin\TabunganUmrahAdminController::class, 'editAccount'])
            ->name('tabungan-umrah.accounts.edit')->middleware('permission:admin.dashboard.view');

        Route::put('/tabungan-umrah/accounts/{account}', [\App\Http\Controllers\Admin\TabunganUmrahAdminController::class, 'updateAccount'])
            ->name('tabungan-umrah.accounts.update')->middleware('permission:admin.dashboard.view');
        // Notifications
        Route::get('/notifications/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])
            ->name('notifications.create')
            ->middleware('permission:admin.notifications.manage');

        Route::post('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])
            ->name('notifications.store')
            ->middleware('permission:admin.notifications.manage');

        Route::prefix('partners')->name('partners.')->group(function () {

            Route::get('/applications', [PartnerApplicationController::class, 'index'])->name('applications.index')->middleware('permission:admin.dashboard.view');
            Route::get('/applications/{application}', [PartnerApplicationController::class, 'show'])->name('applications.show')->middleware('permission:admin.dashboard.view');

            Route::post('/applications/{application}/approve', [PartnerApplicationController::class, 'approve'])->name('applications.approve')->middleware('permission:admin.dashboard.view');
            Route::post('/applications/{application}/reject', [PartnerApplicationController::class, 'reject'])->name('applications.reject')->middleware('permission:admin.dashboard.view');
            Route::delete('/users/{user}', [PartnerApplicationController::class, 'destroyPartner'])->name('users.destroy')->middleware('permission:admin.dashboard.view');

            Route::get('/users', [PartnerApplicationController::class, 'partnerUsers'])->name('users.index')->middleware('permission:admin.dashboard.view');
            Route::post('/users/{user}/suspend', [PartnerApplicationController::class, 'suspend'])->name('users.suspend')->middleware('permission:admin.dashboard.view');
            Route::post('/users/{user}/unsuspend', [PartnerApplicationController::class, 'unsuspend'])->name('users.unsuspend')->middleware('permission:admin.dashboard.view');
            Route::post('/users/{user}/tax', [PartnerApplicationController::class, 'setTax'])->name('users.tax')->middleware('permission:admin.dashboard.view');
            Route::get('/users/{user}', [PartnerApplicationController::class, 'showPartnerUser'])
                ->name('users.show')->middleware('permission:admin.dashboard.view');

            Route::get('/users/{user}/edit', [PartnerApplicationController::class, 'editPartnerUser'])
                ->name('users.edit')->middleware('permission:admin.dashboard.view');

            Route::put('/users/{user}', [PartnerApplicationController::class, 'updatePartnerUser'])
                ->name('users.update')->middleware('permission:admin.dashboard.view');

            Route::prefix('products')->name('products.')->group(function () {
                Route::get('/', [PartnerProductReviewController::class, 'index'])->name('index')->middleware('permission:admin.dashboard.view');

                Route::post('/approve/{type}/{id}', [PartnerProductReviewController::class, 'approve'])
                    ->name('approve')->middleware('permission:admin.dashboard.view');

                Route::post('/reject/{type}/{id}', [PartnerProductReviewController::class, 'reject'])
                    ->name('reject')->middleware('permission:admin.dashboard.view');

                Route::post('/disable/{type}/{id}', [PartnerProductReviewController::class, 'disable'])
                    ->name('disable')->middleware('permission:admin.dashboard.view');
            });
        });
        // Tour
        Route::resource('tour-packages', TourPackageController::class)->middleware('permission:admin.dashboard.view');
        Route::delete('tour-packages/photo/{photo}', [TourPackageController::class, 'deletePhoto'])
            ->name('tour-packages.delete-photo')->middleware('permission:admin.dashboard.view');
        Route::get('seo', [SeoController::class, 'edit'])->name('seo.edit')->middleware('permission:admin.dashboard.view');
        Route::post('seo', [SeoController::class, 'update'])->name('seo.update')->middleware('permission:admin.dashboard.view');
        Route::get('legal-pages', [\App\Http\Controllers\Admin\LegalPagesController::class, 'edit'])
            ->name('legal-pages.edit')->middleware('permission:admin.dashboard.view');
        Route::get('/partner-withdrawals', [\App\Http\Controllers\Admin\PartnerWithdrawalController::class, 'index'])->name('partner_withdrawals.index')->middleware('permission:admin.dashboard.view');
        Route::get('/partner-withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\PartnerWithdrawalController::class, 'show'])->name('partner_withdrawals.show')->middleware('permission:admin.dashboard.view');
        Route::put('/partner-withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\PartnerWithdrawalController::class, 'update'])->name('partner_withdrawals.update')->middleware('permission:admin.dashboard.view');
        Route::delete('/partner-withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\PartnerWithdrawalController::class, 'destroy'])->name('partner_withdrawals.destroy')->middleware('permission:admin.dashboard.view');
        Route::resource('umrah-packages', \App\Http\Controllers\Admin\UmrahPackageController::class)->middleware('permission:admin.dashboard.view');
        Route::delete('umrah-packages/photo/{photo}', [\App\Http\Controllers\Admin\UmrahPackageController::class, 'deletePhoto'])
            ->name('umrah-packages.delete-photo')->middleware('permission:admin.dashboard.view');
        Route::get('categories/{category}/subcategories', [\App\Http\Controllers\Admin\TourCategoryController::class, 'subcategories'])
            ->name('categories.subcategories')->middleware('permission:admin.dashboard.view');
        // MICE
        Route::resource('mice-packages', \App\Http\Controllers\Admin\MicePackageController::class)->middleware('permission:admin.dashboard.view');
        Route::delete('mice-packages/photo/{photo}', [\App\Http\Controllers\Admin\MicePackageController::class, 'deletePhoto'])
            ->name('mice-packages.delete-photo')->middleware('permission:admin.dashboard.view');

        Route::resource('mice-categories', \App\Http\Controllers\Admin\MiceCategoryController::class)->middleware('permission:admin.dashboard.view');
        Route::prefix('affiliate')->name('affiliate.')->group(function () {
            Route::get('/requests', [\App\Http\Controllers\Admin\AffiliateApprovalController::class, 'index'])
                ->name('requests.index')->middleware('permission:admin.dashboard.view');
            Route::get('/requests/{user}', [\App\Http\Controllers\Admin\AffiliateApprovalController::class, 'show'])
                ->name('requests.show')->middleware('permission:admin.dashboard.view');
            Route::get('/orders', [\App\Http\Controllers\Admin\AffiliateOrderController::class, 'index'])
                ->name('orders.index')->middleware('permission:admin.dashboard.view');
            Route::get('/orders/{order}', [\App\Http\Controllers\Admin\AffiliateOrderController::class, 'show'])
                ->name('orders.show')->middleware('permission:admin.dashboard.view');
            Route::get('/withdrawals', [\App\Http\Controllers\Admin\AffiliateWithdrawalController::class, 'index'])
                ->name('withdrawals.index')->middleware('permission:admin.dashboard.view');
            Route::get('/withdrawals/{requestModel}', [\App\Http\Controllers\Admin\AffiliateWithdrawalController::class, 'show'])
                ->name('withdrawals.show')->middleware('permission:admin.dashboard.view');
            Route::post('/withdrawals/{requestModel}/status', [\App\Http\Controllers\Admin\AffiliateWithdrawalController::class, 'updateStatus'])
                ->name('withdrawals.status')->middleware('permission:admin.dashboard.view');

            Route::post('/orders/{order}/commission', [\App\Http\Controllers\Admin\AffiliateOrderController::class, 'setCommission'])
                ->name('orders.commission')->middleware('permission:admin.dashboard.view');



            Route::post('/requests/{user}/approve', [\App\Http\Controllers\Admin\AffiliateApprovalController::class, 'approve'])
                ->name('requests.approve')->middleware('permission:admin.dashboard.view');
            Route::post('/requests/{user}/decline', [\App\Http\Controllers\Admin\AffiliateApprovalController::class, 'decline'])
                ->name('requests.decline')->middleware('permission:admin.dashboard.view');
        });


        Route::resource('umrah-categories', \App\Http\Controllers\Admin\UmrahCategoryController::class);
        Route::post('legal-pages', [\App\Http\Controllers\Admin\LegalPagesController::class, 'update'])
            ->name('legal-pages.update')->middleware('permission:admin.dashboard.view');
        Route::get('/reviews/packages', [AdminReviewController::class, 'packages'])
            ->name('reviews.packages')->middleware('permission:admin.dashboard.view');

        Route::get('users/affiliate', [AffiliateUserController::class, 'index'])
            ->name('users.affiliate.index')->middleware('permission:admin.dashboard.view');

        Route::post('users/affiliate/{user}', [AffiliateUserController::class, 'update'])
            ->name('users.affiliate.update')->middleware('permission:admin.dashboard.view');

        Route::resource('users', AdminUserController::class);
        // Rent Car Package CRUD
        Route::resource('rent-car-packages', RentCarPackageController::class)->middleware('permission:admin.dashboard.view');
        Route::post('system/clear-cache', [SystemController::class, 'clearCache'])
            ->name('system.clear-cache')->middleware('permission:admin.dashboard.view');
        Route::resource('ship-packages', ShipPackageController::class)->middleware('permission:admin.dashboard.view');

        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index')->middleware('permission:admin.dashboard.view');
        Route::post('/payments/bank', [PaymentController::class, 'addBank'])->name('bank.add')->middleware('permission:admin.dashboard.view');
        Route::delete('/payments/bank/{bank}', [PaymentController::class, 'deleteBank'])->name('bank.delete')->middleware('permission:admin.dashboard.view');
        Route::post('/payments/gateway/{gateway}', [PaymentController::class, 'toggleGateway'])
            ->name('payments.toggleGateway')->middleware('permission:admin.dashboard.view');
        Route::put('/payments/unique-code-setting', [PaymentController::class, 'updateUniqueCodeSetting'])
            ->name('payments.unique-code-setting')->middleware('permission:admin.dashboard.view');

        Route::resource('client-logos', \App\Http\Controllers\Admin\ClientLogoController::class)->middleware('permission:admin.dashboard.view');

        // Promo Admin
        Route::resource('promos', PromoController::class)->except(['show'])->middleware('permission:admin.dashboard.view');
        Route::prefix('promos/home-banners')->name('promos.home-banners.')->middleware('permission:admin.dashboard.view')->group(function () {
            Route::get('{section}', [\App\Http\Controllers\Admin\HomePromoBannerController::class, 'index'])->name('index');
            Route::get('{section}/create', [\App\Http\Controllers\Admin\HomePromoBannerController::class, 'create'])->name('create');
            Route::post('{section}', [\App\Http\Controllers\Admin\HomePromoBannerController::class, 'store'])->name('store');

            Route::get('{section}/{banner}/edit', [\App\Http\Controllers\Admin\HomePromoBannerController::class, 'edit'])->name('edit');
            Route::put('{section}/{banner}', [\App\Http\Controllers\Admin\HomePromoBannerController::class, 'update'])->name('update');
            Route::delete('{section}/{banner}', [\App\Http\Controllers\Admin\HomePromoBannerController::class, 'destroy'])->name('destroy');
        });
        // Bank Account Admin
        Route::resource('bank-accounts', BankAccountController::class)->except(['show'])->middleware('permission:admin.dashboard.view');

        Route::resource('articles', ArticleController::class)->middleware('permission:admin.dashboard.view');
        Route::get('home-sections/promo-tours', [\App\Http\Controllers\Admin\HomePromoToursController::class, 'edit'])
            ->name('home-sections.promo-tours.edit')->middleware('permission:admin.dashboard.view');

        Route::post('home-sections/promo-tours', [\App\Http\Controllers\Admin\HomePromoToursController::class, 'update'])
            ->name('home-sections.promo-tours.update')->middleware('permission:admin.dashboard.view');

        // Settings
        Route::get('settings/general', [SettingController::class, 'general'])->name('settings.general')->middleware('permission:admin.dashboard.view');
        Route::post('settings/general', [SettingController::class, 'saveGeneral'])->name('settings.general.save')->middleware('permission:admin.dashboard.view');
        Route::get('settings/home', [HomeSettingController::class, 'edit'])
            ->name('settings.home')
            ->middleware('permission:admin.dashboard.view');

        Route::get('settings/home/articles/search', [HomeSettingController::class, 'searchArticles'])
            ->name('settings.home.articles.search')
            ->middleware('permission:admin.dashboard.view');

        Route::post('settings/home', [HomeSettingController::class, 'update'])
            ->name('settings.home.save')
            ->middleware('permission:admin.dashboard.view');
        Route::post('settings/home/footer-logos', [HomeSettingController::class, 'storeFooterLogo'])
            ->name('settings.home.footer-logos.store')
            ->middleware('permission:admin.dashboard.view');

        Route::put('settings/home/footer-logos/{footerLogo}', [HomeSettingController::class, 'updateFooterLogo'])
            ->name('settings.home.footer-logos.update')
            ->middleware('permission:admin.dashboard.view');

        Route::delete('settings/home/footer-logos/{footerLogo}', [HomeSettingController::class, 'destroyFooterLogo'])
            ->name('settings.home.footer-logos.destroy')
            ->middleware('permission:admin.dashboard.view');
        Route::get('settings/popup', [PopupWidgetController::class, 'edit'])
            ->name('settings.popup.edit')
            ->middleware('permission:admin.dashboard.view');

        Route::post('settings/popup', [PopupWidgetController::class, 'update'])
            ->name('settings.popup.save')
            ->middleware('permission:admin.dashboard.view');

        // Orders (sistem baru)
        Route::get('orders/approved', [AdminOrderController::class, 'approved'])
            ->name('orders.approved')->middleware('permission:admin.dashboard.view');

        Route::get('orders/rejected', [AdminOrderController::class, 'rejected'])
            ->name('orders.rejected')->middleware('permission:admin.dashboard.view');

        // ✅ taruh rekap dulu
        Route::get('orders/rekap', [AdminOrderController::class, 'rekap'])
            ->name('orders.rekap')->middleware('permission:admin.dashboard.view');

        Route::get('orders/rekap/print', [AdminOrderController::class, 'printRekap'])
            ->name('orders.rekap.print')->middleware('permission:admin.dashboard.view');
        Route::get('orders/rekap/print-paid', [AdminOrderController::class, 'printRekapPaid'])
            ->name('orders.rekap.printPaid')->middleware('permission:admin.dashboard.view');

        Route::post('orders/rekap/print-selected', [AdminOrderController::class, 'printRekapSelected'])
            ->name('orders.rekap.printSelected')->middleware('permission:admin.dashboard.view');

        Route::get('orders/{order}/invoice/print', [AdminOrderController::class, 'printInvoice'])
            ->name('orders.invoice.print')->middleware('permission:admin.dashboard.view');

        // ✅ resource terakhir, hanya sekali
        Route::resource('orders', AdminOrderController::class)
            ->only(['index', 'show', 'update', 'destroy']);


        Route::get('/reviews/create', [AdminReviewController::class, 'create'])->name('reviews.create')->middleware('permission:admin.dashboard.view');
        Route::post('/reviews', [AdminReviewController::class, 'store'])->name('reviews.store')->middleware('permission:admin.dashboard.view');


        // Categories
        Route::resource('categories', \App\Http\Controllers\Admin\TourCategoryController::class)->middleware('permission:admin.dashboard.view');
        Route::resource('ship-categories', \App\Http\Controllers\Admin\ShipCategoryController::class)->middleware('permission:admin.dashboard.view');

        Route::resource('rent-car-categories', \App\Http\Controllers\Admin\RentCarCategoryController::class)->middleware('permission:admin.dashboard.view');
        Route::resource(
            'destination-inspirations',
            \App\Http\Controllers\Admin\DestinationInspirationController::class
        )->middleware('permission:admin.dashboard.view');

        Route::resource('documentations', AdminDocumentationController::class)->middleware('permission:admin.dashboard.view');
        //review
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index')->middleware('permission:admin.dashboard.view');
        Route::patch('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve')->middleware('permission:admin.dashboard.view');
        Route::patch('/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject')->middleware('permission:admin.dashboard.view');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.delete')->middleware('permission:admin.dashboard.view');
        Route::get('/reviews/{review}/edit', [AdminReviewController::class, 'edit'])->name('reviews.edit')->middleware('permission:admin.dashboard.view');
        Route::patch('/reviews/{review}', [AdminReviewController::class, 'update'])->name('reviews.update')->middleware('permission:admin.dashboard.view');
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
        Route::get('/notifications', [\App\Http\Controllers\NotificationCenterController::class, 'indexUser'])
            ->name('notifications.index');

        Route::post('/withdrawals', [\App\Http\Controllers\User\AffiliateController::class, 'submitWithdrawal'])
            ->name('withdrawals.submit');
        // Tabungan Umrah
        Route::get('/tabungan-umrah', [\App\Http\Controllers\User\TabunganUmrahController::class, 'index'])
            ->name('tabungan-umrah.index');

        Route::post('/tabungan-umrah/register', [\App\Http\Controllers\User\TabunganUmrahController::class, 'storeRegistration'])
            ->name('tabungan-umrah.register');

        Route::get('/tabungan-umrah/setoran/create', [\App\Http\Controllers\User\TabunganUmrahController::class, 'createDeposit'])
            ->name('tabungan-umrah.deposits.create');

        Route::post('/tabungan-umrah/setoran', [\App\Http\Controllers\User\TabunganUmrahController::class, 'storeDeposit'])
            ->name('tabungan-umrah.deposits.store');

        Route::get('/tabungan-umrah/setoran/{deposit}', [\App\Http\Controllers\User\TabunganUmrahController::class, 'showDeposit'])
            ->name('tabungan-umrah.deposits.show');
        Route::get('/tabungan-umrah/statement/print', [\App\Http\Controllers\User\TabunganUmrahController::class, 'printStatement'])
            ->name('tabungan-umrah.statement.print');


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
        Route::get('/orders/{order}/invoice/print', [\App\Http\Controllers\User\OrderController::class, 'printInvoice'])
            ->name('orders.invoice.print');


        Route::get('/profile', [\App\Http\Controllers\User\ProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::post('/profile', [\App\Http\Controllers\User\ProfileController::class, 'update'])
            ->name('profile.update');
    });

// Partner registration
Route::get('/partner', [PartnerRegistrationController::class, 'create'])->name('partner.register');
Route::post('/partner', [PartnerRegistrationController::class, 'store'])->name('partner.register.store');
Route::get('/partner/pending', [PartnerRegistrationController::class, 'pending'])->name('partner.pending');

// Partner dashboard (after approved)
Route::prefix('partner')->name('partner.')->middleware(['auth', 'role:partner'])->group(function () {
    Route::get('/dashboard', [PartnerDashboardController::class, 'index'])->name('dashboard');
    // ✅ Partner Orders (mirip admin)
    Route::get('/orders', [PartnerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/approved', [PartnerOrderController::class, 'approved'])->name('orders.approved');
    Route::get('/orders/rejected', [PartnerOrderController::class, 'rejected'])->name('orders.rejected');
    Route::get('/orders/{order}', [PartnerOrderController::class, 'show'])->name('orders.show');
    Route::delete('/orders/{order}', [PartnerOrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/withdraw', [\App\Http\Controllers\Partner\WithdrawController::class, 'index'])->name('withdraw.index');
    Route::post('/withdraw', [\App\Http\Controllers\Partner\WithdrawController::class, 'store'])->name('withdraw.store');

    Route::get('/withdraw/requests', [\App\Http\Controllers\Partner\WithdrawController::class, 'requests'])->name('withdraw.requests');
    Route::get('/withdraw/requests/{withdrawal}', [\App\Http\Controllers\Partner\WithdrawController::class, 'show'])->name('withdraw.show');
    Route::delete('/withdraw/requests/{withdrawal}', [\App\Http\Controllers\Partner\WithdrawController::class, 'destroy'])->name('withdraw.destroy');
    Route::get('/notifications', [\App\Http\Controllers\NotificationCenterController::class, 'indexPartner'])
        ->name('notifications.index');

    // ✅ Partner Profile
    Route::get('/profile', [PartnerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [PartnerProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [PartnerProfileController::class, 'updatePassword'])->name('profile.password');
    // agency_paket_tour
    Route::resource('tour-packages', \App\Http\Controllers\Partner\TourPackageController::class);
    Route::get('categories/{category}/subcategories', [\App\Http\Controllers\Partner\TourCategoryController::class, 'subcategories'])
        ->name('categories.subcategories');

    Route::delete('tour-packages/photo/{photo}', [\App\Http\Controllers\Partner\TourPackageController::class, 'deletePhoto'])
        ->name('tour-packages.delete-photo');

    Route::resource('rent-car-packages', \App\Http\Controllers\Partner\RentCarPackageController::class);

    // agency_kapal

    Route::resource('tour-categories', \App\Http\Controllers\Partner\TourCategoryController::class)
        ->except(['show']); // tour categories CRUD

    Route::resource('rent-car-categories', \App\Http\Controllers\Partner\RentCarCategoryController::class)
        ->except(['show']);
});


Route::middleware(['auth'])->group(function () {
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationCenterController::class, 'markRead'])
        ->name('notifications.markRead');

    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationCenterController::class, 'readAll'])
        ->name('notifications.readAll');

    Route::post('/push/subscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'store'])
        ->name('push.subscribe');
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
    cookie()->queue(cookie('locale', $locale, 60 * 24 * 30));
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
Route::get('/paket-tour/{categorySlug?}/{subcategorySlug?}', [TourController::class, 'index'])
    ->where(['categorySlug' => '[A-Za-z0-9\-]+', 'subcategorySlug' => '[A-Za-z0-9\-]+'])
    ->name('tours.index');

Route::get('/dokumentasi', [FrontDocumentationController::class, 'tour'])->name('docs');
Route::get('/dokumentasi/sewa-kapal', [FrontDocumentationController::class, 'ship'])->name('docs.ship');
Route::get('/dokumentasi/umrah', [FrontDocumentationController::class, 'umrah'])->name('docs.umrah');
Route::view('/about', 'front.pages.about')->name('about');

Route::post('/review', [ReviewController::class, 'store'])
    ->middleware('throttle:3,10')
    ->name('review.store');


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

Route::get('/__tools/translate/tour/backfill', [\App\Http\Controllers\Tools\TourTranslateBackfillController::class, 'run']);
