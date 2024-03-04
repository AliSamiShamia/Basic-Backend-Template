<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'slug' => fake()->slug(),
            'sku' => fake()->slug(6, false),
            'description' => fake()->text(),
            'brief' => fake()->text(50),
            'price' => fake()->randomFloat(3, 0, 1000),
            'pre_price' => fake()->randomFloat(3, 0, 1000),
            'weight' => fake()->randomFloat(2, 0, 10),
            'stock' => fake()->randomFloat(3, 0, 100),
            'is_live' => true
        ];
    }
}
