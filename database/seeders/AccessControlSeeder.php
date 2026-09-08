<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccessControlSeeder extends Seeder
{
    /**
     * Buat role dan akun awal tanpa memasukkan data demo survey.
     */
    public function run(): void
    {
        $adminRole = Role::updateOrCreate(
            ['code' => 'admin'],
            ['name' => 'Administrator']
        );

        $userRole = Role::updateOrCreate(
            ['code' => 'user'],
            ['name' => 'Regular User']
        );

        $adminConfig = config('app.seed_users.admin');
        $admin = User::updateOrCreate(
            ['email' => $adminConfig['email']],
            [
                'name' => $adminConfig['name'],
                'password' => Hash::make($adminConfig['password']),
            ]
        );

        $admin->roles()->syncWithoutDetaching([$adminRole->id => [
            'is_active' => true,
            'assigned_at' => now(),
        ]]);

        $userConfig = config('app.seed_users.user');
        $user = User::updateOrCreate(
            ['email' => $userConfig['email']],
            [
                'name' => $userConfig['name'],
                'password' => Hash::make($userConfig['password']),
            ]
        );

        $user->roles()->syncWithoutDetaching([$userRole->id => [
            'is_active' => true,
            'assigned_at' => now(),
        ]]);

        $this->command?->info('Role dan akun awal berhasil di-seed.');
    }
}
