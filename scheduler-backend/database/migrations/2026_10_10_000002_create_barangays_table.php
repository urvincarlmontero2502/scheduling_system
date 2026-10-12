<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Creates a barangays table and seeds it with the 15 default barangays.
// This allows dynamic management of barangays instead of hardcoding them in the frontend.
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('barangays')) {
            Schema::create('barangays', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });

            // Seed default barangays
            $barangays = [
                'A. Beltran',
                'Baleguian',
                'Bangonay',
                'Bunga',
                'Colorado',
                'Cuyago',
                'Libas',
                'Magdalooc',
                'Magsaysay',
                'Maraiging',
                'Poblacion',
                'San Jose',
                'San Pablo',
                'San Vicente',
                'Santo Nino',
            ];

            foreach ($barangays as $brgy) {
                DB::insert('INSERT INTO barangays (name, created_at, updated_at) VALUES (?, ?, ?)',
                    [$brgy, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]
                );
            }
        }
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS barangays');
    }
};
