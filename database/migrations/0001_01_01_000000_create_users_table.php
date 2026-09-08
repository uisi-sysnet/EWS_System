<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            
            $table->string('first_name', 80);
            $table->string('last_name', 80);

            $table->string('email', 150)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password');

            $table->string('contact_number', 20)->nullable();
            $table->string('position', 100)->nullable();
            
            $table->enum('user_level', ['superadmin', 'admin', 'user'])
                  ->default('user');

            // Very useful fields
            $table->boolean('active')->default(true);
            $table->boolean('first_login')->default(true);
            $table->timestamp('login_at')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->boolean('revoked')->default(false);

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();        
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};