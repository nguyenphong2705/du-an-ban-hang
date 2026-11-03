<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('password');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('customer')->after('is_admin');
            }
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->integer('discount_percent');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('min_order_amount')->default(0);
            $table->date('expires_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'coupon_code')) {
                $table->string('coupon_code')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'discount_amount')) {
                $table->unsignedBigInteger('discount_amount')->default(0)->after('coupon_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['coupon_code', 'discount_amount']);
        });
        Schema::dropIfExists('coupons');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'role']);
        });
    }
};
