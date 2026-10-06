<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('barangays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('properties')->nullable();
            // The app only displays and exports GeoJSON; spatial SQL is not used.
            $table->json('boundary');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('barangays');
    }
};
