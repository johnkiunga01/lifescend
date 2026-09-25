<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->decimal('price_eur', 10, 2)->nullable()->after('price');
            $table->decimal('discount_price_eur', 10, 2)->nullable()->after('discount_price');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->dropColumn(['price_eur', 'discount_price_eur']);
        });
    }
};
