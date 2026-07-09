<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Artist\Models\ArtistProfile;
use Modules\Music\Models\Genre;
use Modules\Music\Models\Album;
use Modules\Music\Models\Song;
use Modules\Playlist\Models\Playlist;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Setup Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $artistRole = Role::firstOrCreate(['name' => 'Artist']);
        $listenerRole = Role::firstOrCreate(['name' => 'Listener']);

        // 2. Create Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role' => 'Admin',
                'status' => 'Active',
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        // 3. Create Genres
        $pop = Genre::firstOrCreate(['slug' => 'pop'], ['name' => 'Pop']);
        $rock = Genre::firstOrCreate(['slug' => 'rock'], ['name' => 'Rock']);

        // 4. Create Artists
        $artistUser = User::firstOrCreate(
            ['email' => 'artist@example.com'],
            [
                'name' => 'Artist Demo',
                'password' => Hash::make('password'),
                'role' => 'Artist',
                'status' => 'Active',
                'email_verified_at' => now(),
            ]
        );
        $artistUser->assignRole($artistRole);

        $artistProfile = ArtistProfile::firstOrCreate(
            ['user_id' => $artistUser->id],
            [
                'stage_name' => 'Demo Stage Name',
                'bio' => 'This is a demo artist.',
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        // 5. Create Listeners
        $listenerUser = User::firstOrCreate(
            ['email' => 'listener@example.com'],
            [
                'name' => 'Listener Demo',
                'password' => Hash::make('password'),
                'role' => 'Listener',
                'status' => 'Active',
                'email_verified_at' => now(),
            ]
        );
        $listenerUser->assignRole($listenerRole);

        // 6. Create Album
        $album = Album::firstOrCreate(
            ['artist_id' => $artistProfile->id, 'title' => 'Demo Album'],
            [
                'type' => 'Album',
                'status' => 'Published',
                'release_date' => now(),
            ]
        );

        // 7. Create Songs
        for ($i = 1; $i <= 10; $i++) {
            Song::create([
                'artist_id' => $artistProfile->id,
                'album_id' => $album->id,
                'genre_id' => $pop->id,
                'title' => 'Demo Song ' . $i,
                'hls_path' => 'preview/demo.m3u8',
                'duration' => 180,
                'status' => 'Approved',
                'original_size' => 1024,
                'bitrate' => 128,
                'sample_rate' => 44100,
                'channels' => 2,
                'checksum' => hash('sha256', 'demo song ' . $i),
                'play_count' => 0,
            ]);
        }

        // 8. Create Playlists
        for ($i = 1; $i <= 3; $i++) {
            Playlist::create([
                'user_id' => $listenerUser->id,
                'title' => 'Demo Playlist ' . $i,
                'type' => 'user',
                'privacy' => 'Public',
            ]);
        }
    }
}
