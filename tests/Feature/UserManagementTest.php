<?php

use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    Role::create(['code' => 'admin', 'name' => 'Administrator']);
    Role::create(['code' => 'user', 'name' => 'Pengguna Biasa']);
});

function userWithRole(string $roleCode = 'user', array $attributes = []): User
{
    $user = User::factory()->create($attributes);
    $role = Role::where('code', $roleCode)->firstOrFail();
    $user->roles()->attach($role, ['is_active' => true, 'assigned_at' => now()]);

    return $user;
}

test('administrator can create a user with the selected role', function () {
    $admin = userWithRole('admin');

    $response = $this->actingAs($admin)->post(route('users.store'), [
        'name' => 'Akun Baru',
        'email' => 'akun-baru@example.test',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'user',
        'is_active' => '1',
    ]);

    $created = User::where('email', 'akun-baru@example.test')->firstOrFail();

    $response->assertRedirect(route('users.index'));
    $this->assertDatabaseHas('users', ['id' => $created->id, 'is_active' => true]);
    $this->assertDatabaseHas('user_roles', ['user_id' => $created->id, 'role_id' => Role::where('code', 'user')->value('id')]);
});

test('administrator can change a user role and status', function () {
    $admin = userWithRole('admin');
    $user = userWithRole();

    $response = $this->actingAs($admin)->put(route('users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'role' => 'admin',
        'is_active' => '0',
    ]);

    $response->assertRedirect(route('users.index'));
    $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => false]);
    $this->assertDatabaseHas('user_roles', ['user_id' => $user->id, 'role_id' => Role::where('code', 'admin')->value('id')]);
    $this->assertDatabaseMissing('user_roles', ['user_id' => $user->id, 'role_id' => Role::where('code', 'user')->value('id')]);
});

test('disabled users cannot authenticate', function () {
    $user = userWithRole('user', ['is_active' => false]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
});

test('the last active administrator cannot be disabled or deleted', function () {
    $admin = userWithRole('admin');
    $otherAdmin = userWithRole('admin');

    $this->actingAs($otherAdmin)->patch(route('users.status', $admin));
    $this->assertDatabaseHas('users', ['id' => $admin->id, 'is_active' => false]);

    $this->actingAs($admin)->patch(route('users.status', $otherAdmin));
    $this->actingAs($admin)->delete(route('users.destroy', $otherAdmin));

    $this->assertDatabaseHas('users', ['id' => $otherAdmin->id]);
    $this->assertDatabaseHas('users', ['id' => $otherAdmin->id, 'is_active' => true]);
});
