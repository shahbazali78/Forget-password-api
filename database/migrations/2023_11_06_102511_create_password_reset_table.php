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
        Schema::create('password_reset', function (Blueprint $table) {
            $table->id();
            $table->string('otp')->index();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('expires_at')->default(now()->addMinutes(15));
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset');
    }
};
