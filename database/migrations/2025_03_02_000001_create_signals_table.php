<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->string('name');
            $table->string('oid')->index();               
            $table->unsignedBigInteger('signal_id');

            $table->unsignedInteger('duration')->nullable();

            $table->char('beacon_color_1', 7)->nullable();
            $table->unsignedInteger('delay_1')->nullable();

            $table->char('beacon_color_2', 7)->nullable();
            $table->unsignedInteger('delay_2')->nullable();

            $table->char('button_color', 7)->nullable();     
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signals');
    }
};