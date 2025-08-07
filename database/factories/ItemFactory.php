<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Album;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'album_id' => Album::inRandomOrder()->value('id') ?? Album::factory(), // ensure album exists
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'keyword' => implode(', ', $this->faker->words(3)),
            'media_url' => 'images/upload/' . $this->faker->image('public/storage/images/upload', 640, 480, null, false),
            'status' => $this->faker->boolean(),
            'created_by' => User::inRandomOrder()->value('id') ?? User::factory(),
        ];
    }
}
