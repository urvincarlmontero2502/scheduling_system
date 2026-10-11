<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates a temporary tokens table for pending Google OAuth registrations.
     * 
     * When a new Google user doesn't exist in the system, instead of creating
     * the user immediately, we store their Google profile data (name, email,
     * google_id, avatar) with a temporary token. The user is redirected to
     * /register/barangay to select their barangay. When they submit the form,
     * the completeGoogleRegistration method uses this token to look up the
     * pre-verified Google data, creates the permanent user record with the
     * selected barangay, and issues their Sanctum token.
     */
    public function up(): void
    {
        if (!Schema::hasTable('google_oauth_temp_tokens')) {
            Schema::create('google_oauth_temp_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('token')->unique();
                $table->string('email')->unique();
                $table->string('google_id')->nullable();
                $table->string('full_name')->nullable();
                $table->string('avatar')->nullable();
                $table->timestamp('expires_at');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('google_oauth_temp_tokens');
    }
};
