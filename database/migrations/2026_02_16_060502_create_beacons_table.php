<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beacons', function (Blueprint $table) {
            $table->id();

            $table->string('name', 120);
            $table->foreignId('location_id')
                ->constrained()
                ->onDelete('restrict');

            $table->string('oid', 100)->nullable()->unique();
            $table->string('beacon_id', 60)->nullable()->unique();
            $table->string('group', 120)->nullable();

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('status')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('is_door_open')->nullable();  
            $table->timestamp('last_opened_at')->nullable();
            $table->timestamps();

            $table->index('group');
            $table->index('location_id');
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beacons');
    }
};
