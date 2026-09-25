<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('reservation_type')->default('individual')->after('status');
            $table->string('company_name')->nullable()->after('reservation_type');
            $table->unsignedInteger('attendee_count')->default(1)->after('company_name');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['reservation_type', 'company_name', 'attendee_count']);
        });
    }
};
