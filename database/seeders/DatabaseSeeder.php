<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

       // 1. Buat Role
        $adminRole = Role::updateOrCreate(
            ['code' => 'admin'],
            ['name' => 'Administrator']
        );

        $userRole = Role::updateOrCreate(
            ['code' => 'user'],
            ['name' => 'Regular User']
        );

        // 2. Buat Akun Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('password'), // Silakan ganti passwordnya
            ]
        );

        // Hubungkan Admin ke Role Admin di tabel pivot
        // syncWithoutDetaching mencegah duplikasi jika seeder dijalankan ulang
        $admin->roles()->syncWithoutDetaching([$adminRole->id => [
            'is_active' => true,
            'assigned_at' => now()
        ]]);

        // 3. Buat Akun Reguler
        $reguler = User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'User Biasa',
                'password' => Hash::make('password'),
            ]
        );

        // Hubungkan User ke Role User
        $reguler->roles()->syncWithoutDetaching([$userRole->id => [
            'is_active' => true,
            'assigned_at' => now()
        ]]);

        $this->call([
            PenggunaLulusanSeeder::class,
            LulusanSeeder::class,
        ]);
    }
}
