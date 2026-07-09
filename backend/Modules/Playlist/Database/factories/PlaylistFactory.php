<?php

namespace Modules\Playlist\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Playlist\Models\Playlist;

class PlaylistFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Playlist::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'privacy' => 'Public',
            'type' => 'user',
        ];
    }
}
