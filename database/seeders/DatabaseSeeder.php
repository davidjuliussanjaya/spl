<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Data hasil impor Oracle dapat membawa nilai ID eksplisit. PostgreSQL
        // tidak otomatis menaikkan sequence ketika ID tersebut diimpor.
        $this->call([
            PostgreSqlSequenceSeeder::class,
            AccessControlSeeder::class,
            PenggunaLulusanSeeder::class,
            LulusanSeeder::class,
            DraftInstrumenUniversitas2026Seeder::class,
            SurveyBulkCreateSeeder::class,
            SurveyResponseSeeder::class,
        ]);
    }
}
