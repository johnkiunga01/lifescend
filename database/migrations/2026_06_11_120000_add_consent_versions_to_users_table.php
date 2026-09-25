<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('accepted_terms_version')->nullable()->after('status');
            $table->string('accepted_privacy_version')->nullable()->after('accepted_terms_version');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['accepted_terms_version', 'accepted_privacy_version']);
        });
    }
};
