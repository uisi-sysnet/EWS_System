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
            $table->geometry('boundary', 'POLYGON', 4326); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('barangays');
    }
};