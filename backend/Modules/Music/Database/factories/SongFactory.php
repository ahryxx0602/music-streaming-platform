<?php

namespace Modules\Music\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Music\Models\Song;

class SongFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Song::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'lyrics' => $this->faker->paragraphs(3, true),
            'duration' => $this->faker->numberBetween(180, 300),
            'play_count' => $this->faker->numberBetween(0, 10000),
            'status' => 'Approved',
            'bitrate' => 320,
            'sample_rate' => 44100,
            'channels' => 2,
        ];
    }
}
