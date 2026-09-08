<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('name')->nullable();
            $table->string('endpoint_url');
            $table->string('header_name');
            // Use TEXT to store encrypted values (much longer than plaintext)
            $table->text('header_value');
            
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('revoked')->default(false);
            
            $table->timestamps();

            // Unique index removed because encrypted values are not deterministic
            // Duplicate detection is now handled at application level
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_connections');
    }
};
