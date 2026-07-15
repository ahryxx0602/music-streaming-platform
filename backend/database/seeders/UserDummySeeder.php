<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Modules\Artist\Models\ArtistProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $artistRole = Role::firstOrCreate(['name' => 'Artist']);
        $listenerRole = Role::firstOrCreate(['name' => 'Listener']);

        $password = Hash::make('password123'); // Chung mật khẩu để dễ test

        // Seed 3 Admins
        for ($i = 1; $i <= 3; $i++) {
            $uniqueId = uniqid();
            $user = User::create([
                'name' => fake()->name(),
                'email' => "admin_test{$i}_{$uniqueId}@example.com",
                'email_verified_at' => now(),
                'password' => $password,
                'role' => 'admin',
                'status' => 'Active',
                'remember_token' => Str::random(10),
            ]);
            $user->assignRole($adminRole);
        }

        // Seed 20 Artists
        for ($i = 1; $i <= 20; $i++) {
            $user = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => $password,
                'role' => 'artist',
                'status' => fake()->randomElement(['Active', 'Active', 'Suspended', 'Banned']),
                'remember_token' => Str::random(10),
            ]);
            $user->assignRole($artistRole);

            ArtistProfile::create([
                'user_id' => $user->id,
                'stage_name' => fake()->name() . ' Band',
                'bio' => fake()->paragraph(),
                'is_verified' => fake()->boolean(70),
                'verified_at' => now(),
            ]);
        }

        // Seed 40 Listeners
        for ($i = 1; $i <= 40; $i++) {
            $user = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => $password,
                'role' => 'listener',
                'status' => fake()->randomElement(['Active', 'Active', 'Active', 'Banned']),
                'remember_token' => Str::random(10),
            ]);
            $user->assignRole($listenerRole);
        }
    }
}
