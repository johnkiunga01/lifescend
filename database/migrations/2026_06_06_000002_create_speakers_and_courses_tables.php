<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('speakers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('set null');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();
            $table->string('photo')->nullable();
            $table->json('social_links')->nullable();
            $table->timestamps();
        });

        Schema::create('speaker_event', function (Blueprint $table) {
            $table->id();
            $table->foreignId('speaker_id')->constrained('speakers')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->boolean('featured')->default(false);
            $table->timestamps();
        });

        Schema::create('training_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->string('training_type'); // online, inhouse
            $table->date('preferred_date');
            $table->string('status')->default('pending'); // pending, approved, rejected, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_requests');
        Schema::dropIfExists('speaker_event');
        Schema::dropIfExists('speakers');
    }
};
