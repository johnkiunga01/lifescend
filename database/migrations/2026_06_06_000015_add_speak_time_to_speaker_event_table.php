<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('speaker_event', function (Blueprint $table) {
            $table->string('speak_time')->nullable()->after('featured');
        });
    }

    public function down(): void
    {
        Schema::table('speaker_event', function (Blueprint $table) {
            $table->dropColumn('speak_time');
        });
    }
};
