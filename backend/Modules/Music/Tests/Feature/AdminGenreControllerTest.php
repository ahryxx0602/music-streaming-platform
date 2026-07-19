<?php

namespace Modules\Music\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Music\Models\Genre;
use Tests\TestCase;

class AdminGenreControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        Storage::fake('public');
    }

    /** @test */
    public function test_admin_can_get_genres_list()
    {
        Genre::create([
            'name' => 'Pop',
            'slug' => 'pop',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/genres');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => []]);
    }

    /** @test */
    public function test_admin_can_create_genre()
    {
        $payload = [
            'name' => 'Rock',
            'description' => 'Rock music',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/genres', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('genres', [
            'name' => 'Rock',
            'slug' => 'rock', // Assuming the controller generates slug
        ]);
    }

    /** @test */
    public function test_admin_can_update_genre()
    {
        $genre = Genre::create([
            'name' => 'Jazz',
            'slug' => 'jazz',
        ]);

        $payload = [
            'name' => 'Jazz Updated',
            'is_active' => false,
        ];

        $response = $this->actingAs($this->admin)->putJson("/api/v1/admin/genres/{$genre->id}", $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => 'Jazz Updated',
        ]);
    }
}
