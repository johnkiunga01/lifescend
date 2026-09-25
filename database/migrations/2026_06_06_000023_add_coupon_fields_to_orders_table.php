<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('stripe_payment_intent_id')->constrained('coupons')->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0)->after('total_amount');
            $table->decimal('total_amount', 10, 2)->change(); // already decimal, ensure
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['coupon_id', 'discount_amount']);
        });
    }
};
