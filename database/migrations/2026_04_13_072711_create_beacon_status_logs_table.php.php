<?php
// database/migrations/2024_01_01_000001_create_beacon_status_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
Schema::create('beacon_status_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('beacon_id')->constrained()->onDelete('cascade');
    $table->string('beacon_identifier');
    $table->string('beacon_name');
    $table->enum('status', ['online', 'offline']);
    $table->timestamp('event_time');
    $table->decimal('duration_seconds', 15, 6)->nullable();
    $table->timestamps();

    $table->index(['beacon_id', 'event_time']);
    $table->index('beacon_identifier');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('beacon_status_logs');
    }
};