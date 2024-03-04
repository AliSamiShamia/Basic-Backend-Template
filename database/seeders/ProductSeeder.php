<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::factory()->count(10)->create();

        foreach ($products as $product) {
            $product->Categories()->create([
                'category_id' => Category::query()->get()->random()->id
            ]);
            $product->images()->create([
                "url" => "https://images.pexels.com/photos/7764611/pexels-photo-7764611.jpeg?auto=compress&cs=tinysrgb&w=600",
                "thumb_url" => "https://images.pexels.com/photos/7764611/pexels-photo-7764611.jpeg?auto=compress&cs=tinysrgb&w=600",
                "type" => "image",
                "mime_type" => "image",
                "priority" => 1
            ]);
        }
    }
}
