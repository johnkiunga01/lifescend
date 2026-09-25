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
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('name');
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('session_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // keynote, workshop, panel, presentation
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('room')->nullable();
            $table->integer('capacity')->nullable();
            $table->foreignId('track_id')->nullable()->constrained('tracks')->onDelete('set null');
            $table->foreignId('session_type_id')->nullable()->constrained('session_types')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('session_speaker', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('sessions')->onDelete('cascade');
            $table->foreignId('speaker_id')->constrained('speakers')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->string('level'); // platinum, gold, silver, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsors');
        Schema::dropIfExists('session_speaker');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('session_types');
        Schema::dropIfExists('tracks');
    }
};
