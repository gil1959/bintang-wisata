<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('footer_logos')) {
            Schema::create('footer_logos', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('image_path');
                $table->string('url')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('popup_widgets')) {
            Schema::create('popup_widgets', function (Blueprint $table) {
                $table->id();
                $table->boolean('is_enabled')->default(false);
                $table->string('name');
                $table->string('title')->nullable();
                $table->string('body_format')->default('html');
                $table->longText('body_html')->nullable();
                $table->text('body_text')->nullable();
                $table->string('image_path')->nullable();
                $table->string('primary_button_text')->nullable();
                $table->string('primary_button_link')->nullable();
                $table->string('secondary_button_text')->nullable();
                $table->string('secondary_button_link')->nullable();
                $table->json('include_paths')->nullable();
                $table->json('exclude_paths')->nullable();
                $table->boolean('show_on_mobile')->default(true);
                $table->boolean('show_on_desktop')->default(true);
                $table->integer('delay_seconds')->default(0);
                $table->string('frequency')->default('always');
                $table->dateTime('start_at')->nullable();
                $table->dateTime('end_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tabungan_umrah_accounts')) {
            Schema::create('tabungan_umrah_accounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('full_name');
                $table->string('whatsapp')->nullable();
                $table->string('saving_type')->default('flexible');
                $table->string('status')->default('pending');
                $table->decimal('target_amount', 15, 2)->default(0);
                $table->date('target_departure_date')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->dateTime('rejected_at')->nullable();
                $table->text('rejected_reason')->nullable();
                $table->dateTime('suspended_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tabungan_umrah_deposits')) {
            Schema::create('tabungan_umrah_deposits', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('account_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('payment_method_id')->nullable();
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('proof_image')->nullable();
                $table->string('status')->default('pending');
                $table->text('note')->nullable();
                $table->dateTime('submitted_at')->nullable();
                $table->dateTime('verified_at')->nullable();
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('push_subscriptions')) {
            Schema::create('push_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->text('endpoint');
                $table->string('public_key')->nullable();
                $table->string('auth_token')->nullable();
                $table->string('content_encoding')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('affiliate_links')) {
            Schema::create('affiliate_links', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('name')->nullable();
                $table->string('code')->nullable()->index();
                $table->string('product_type')->nullable();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->string('product_slug')->nullable();
                $table->string('product_name')->nullable();
                $table->unsignedBigInteger('promo_id')->nullable();
                $table->string('promo_code')->nullable();
                $table->string('platform')->nullable();
                $table->string('platform_id')->nullable();
                $table->string('utm_source')->nullable();
                $table->string('utm_medium')->nullable();
                $table->string('utm_campaign')->nullable();
                $table->string('utm_content')->nullable();
                $table->string('utm_term')->nullable();
                $table->text('sales_url')->nullable();
                $table->text('checkout_url')->nullable();
                $table->integer('clicks')->default(0);
                $table->integer('conversions')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('affiliate_user_coupons')) {
            Schema::create('affiliate_user_coupons', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('promo_id');
                $table->string('alias_name')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('affiliate_withdrawal_requests')) {
            Schema::create('affiliate_withdrawal_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('payout_method')->nullable();
                $table->string('payout_provider')->nullable();
                $table->string('account_name')->nullable();
                $table->string('account_number')->nullable();
                $table->string('status')->default('pending');
                $table->text('admin_note')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->dateTime('reviewed_at')->nullable();
                $table->dateTime('paid_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('affiliate_withdrawal_items')) {
            Schema::create('affiliate_withdrawal_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('withdrawal_request_id');
                $table->unsignedBigInteger('order_id');
                $table->decimal('amount', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('partner_applications')) {
            Schema::create('partner_applications', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->text('reason')->nullable();
                $table->string('identity_type')->nullable();
                $table->string('identity_file_path')->nullable();
                $table->string('legal_document_path')->nullable();
                $table->string('password_hash')->nullable();
                $table->text('password_enc')->nullable();
                $table->string('status')->default('pending');
                $table->dateTime('submitted_at')->nullable();
                $table->dateTime('reviewed_at')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->text('review_note')->nullable();
                $table->string('partner_type')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('bank_account_number')->nullable();
                $table->string('bank_account_holder')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('partner_withdrawal_requests')) {
            Schema::create('partner_withdrawal_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('partner_id');
                $table->bigInteger('amount')->default(0);
                $table->string('email')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('account_number')->nullable();
                $table->string('account_holder')->nullable();
                $table->string('status')->default('pending');
                $table->text('admin_note')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->dateTime('reviewed_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_withdrawal_requests');
        Schema::dropIfExists('partner_applications');
        Schema::dropIfExists('affiliate_withdrawal_items');
        Schema::dropIfExists('affiliate_withdrawal_requests');
        Schema::dropIfExists('affiliate_user_coupons');
        Schema::dropIfExists('affiliate_links');
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('tabungan_umrah_deposits');
        Schema::dropIfExists('tabungan_umrah_accounts');
        Schema::dropIfExists('popup_widgets');
        Schema::dropIfExists('footer_logos');
    }
};
