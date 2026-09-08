<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('sirens', function (Blueprint $table) {
        $table->id();
        $table->string('name')->nullable();

        // Remove the old 'location' string column and add foreign key
        // $table->string('location')->nullable(); // ❌ remove this line
        $table->foreignId('location_id')
              ->nullable()
              ->constrained('locations')
              ->nullOnDelete();

        $table->string('oid')->unique()->nullable();
        $table->string('siren_id')->nullable();
        $table->string('group')->nullable();
        $table->decimal('latitude', 10, 7)->nullable();
        $table->decimal('longitude', 10, 7)->nullable();
        $table->boolean('enabled')->default(true);
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('sirens');
    }
};