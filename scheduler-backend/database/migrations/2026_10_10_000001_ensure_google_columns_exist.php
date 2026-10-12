<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Ensure google_id and email_verified_at columns exist in the users table.
// This is a safety net migration to handle cases where the original migration
// didn't run properly on Supabase PostgreSQL.
return new class extends Migration
{
    public function up(): void
    {
        $tableName = 'users';

        // Get existing columns — works on both SQLite and PostgreSQL
        $rawColumns = DB::connection()->getSchemaBuilder()->getColumnListing($tableName);
        $existingColumns = array_map(function ($col) {
            // SQLite returns plain strings; PostgreSQL may return objects/arrays
            if (is_array($col)) {
                return $col['name'] ?? $col['column_name'] ?? '';
            }
            if (is_object($col)) {
                return $col->name ?? $col->column_name ?? '';
            }
            return (string) $col;
        }, $rawColumns);

        if (!in_array('google_id', $existingColumns)) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('google_id')->nullable()->index();
            });
        }

        if (!in_array('email_verified_at', $existingColumns)) {
            DB::statement('ALTER TABLE users ADD COLUMN email_verified_at TIMESTAMP NULL');
        }

        if (!in_array('pending_email', $existingColumns)) {
            DB::statement('ALTER TABLE users ADD COLUMN pending_email VARCHAR(255) NULL');
        }

        if (!in_array('email_verification_token', $existingColumns)) {
            DB::statement('ALTER TABLE users ADD COLUMN email_verification_token VARCHAR(64) NULL');
        }

        if (!in_array('email_verification_sent_at', $existingColumns)) {
            DB::statement('ALTER TABLE users ADD COLUMN email_verification_sent_at TIMESTAMP NULL');
        }

        if (!in_array('profile_image', $existingColumns)) {
            DB::statement('ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL');
        }

        if (!in_array('barangay', $existingColumns)) {
            DB::statement('ALTER TABLE users ADD COLUMN barangay VARCHAR(255) NULL');
        }

        if (!in_array('department', $existingColumns)) {
            DB::statement('ALTER TABLE users ADD COLUMN department VARCHAR(255) NULL');
        }

        if (!in_array('avatar', $existingColumns)) {
            DB::statement('ALTER TABLE users ADD COLUMN avatar VARCHAR(500) NULL');
        }
    }

    public function down(): void
    {
        // No-op: can't safely drop columns that might have data
    }
};
