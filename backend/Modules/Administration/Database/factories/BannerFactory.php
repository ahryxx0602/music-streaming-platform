<?php

namespace Modules\Administration\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Administration\Models\Banner;

class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'image_url' => 'banners/dummy.jpg',
            'target_url' => $this->faker->url(),
            'order' => 0,
            'is_active' => true,
        ];
    }
}
