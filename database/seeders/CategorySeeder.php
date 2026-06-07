<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Burgers', 'slug' => 'burgers', 'description' => 'Juicy burgers', 'sort_order' => 1],
            ['name' => 'Pizza', 'slug' => 'pizza', 'description' => 'Fresh pizzas', 'sort_order' => 2],
            ['name' => 'Salads', 'slug' => 'salads', 'description' => 'Healthy salads', 'sort_order' => 3],
            ['name' => 'Drinks', 'slug' => 'drinks', 'description' => 'Cold & hot drinks', 'sort_order' => 4],
            ['name' => 'Desserts', 'slug' => 'desserts', 'description' => 'Sweet treats', 'sort_order' => 5],
            ['name' => 'Sides', 'slug' => 'sides', 'description' => 'Sides & extras', 'sort_order' => 6],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['is_active' => true])
            );
        }
    }
}
