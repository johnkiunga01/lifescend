<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_requests', function (Blueprint $table) {
            $table->string('event_type')->nullable()->after('training_type');
            $table->foreignId('venue_id')->nullable()->after('event_type')->constrained('venues')->nullOnDelete();
            $table->dateTime('start_date')->nullable()->after('preferred_date');
            $table->dateTime('end_date')->nullable()->after('start_date');
            $table->string('timezone')->nullable()->after('end_date');
            $table->string('cover_image')->nullable()->after('timezone');
            $table->integer('capacity')->nullable()->after('cover_image');
            $table->string('payment_type')->nullable()->after('capacity');
            $table->json('topics')->nullable()->after('payment_type');
            $table->json('keynotes')->nullable()->after('topics');
            $table->json('sponsor_ids')->nullable()->after('keynotes');
            $table->json('pricing')->nullable()->after('sponsor_ids');
        });
    }

    public function down(): void
    {
        Schema::table('training_requests', function (Blueprint $table) {
            $table->dropForeign(['venue_id']);
            $table->dropColumn([
                'event_type',
                'venue_id',
                'start_date',
                'end_date',
                'timezone',
                'cover_image',
                'capacity',
                'payment_type',
                'topics',
                'keynotes',
                'sponsor_ids',
                'pricing',
            ]);
        });
    }
};
