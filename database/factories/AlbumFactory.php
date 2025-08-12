<?php

namespace Database\Factories;

use App\Models\Album;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlbumFactory extends Factory
{
    protected $model = Album::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'location' => $this->faker->city(),
            'keyword' => implode(', ', $this->faker->words(3)),
            'status' => $this->faker->boolean(80), // 80% chance active
        ];
    }
}
