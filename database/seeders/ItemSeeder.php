<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Burgers' => [
                ['name' => 'Classic Burger', 'price' => 8.99, 'description' => 'Beef patty, lettuce, tomato', 'is_featured' => true],
                ['name' => 'Cheese Burger', 'price' => 9.99, 'description' => 'Double cheese, pickles', 'is_featured' => true],
                ['name' => 'Chicken Burger', 'price' => 7.99, 'description' => 'Crispy chicken fillet', 'is_featured' => false],
                ['name' => 'Veggie Burger', 'price' => 7.49, 'description' => 'Plant-based patty', 'is_featured' => false],
            ],
            'Pizza' => [
                ['name' => 'Margherita', 'price' => 10.99, 'description' => 'Tomato, mozzarella, basil', 'is_featured' => true],
                ['name' => 'Pepperoni', 'price' => 12.99, 'description' => 'Pepperoni, cheese', 'is_featured' => true],
                ['name' => 'BBQ Chicken', 'price' => 13.99, 'description' => 'BBQ sauce, chicken', 'is_featured' => false],
                ['name' => 'Hawaiian', 'price' => 11.99, 'description' => 'Ham, pineapple', 'is_featured' => false],
            ],
            'Salads' => [
                ['name' => 'Caesar Salad', 'price' => 6.99, 'description' => 'Romaine, parmesan, croutons', 'is_featured' => false],
                ['name' => 'Greek Salad', 'price' => 7.49, 'description' => 'Feta, olives, cucumber', 'is_featured' => false],
            ],
            'Drinks' => [
                ['name' => 'Cola', 'price' => 2.49, 'description' => 'Regular or diet', 'is_featured' => false],
                ['name' => 'Lemonade', 'price' => 2.99, 'description' => 'Fresh lemonade', 'is_featured' => false],
                ['name' => 'Coffee', 'price' => 2.99, 'description' => 'Hot or iced', 'is_featured' => false],
                ['name' => 'Water', 'price' => 1.49, 'description' => 'Bottled water', 'is_featured' => false],
            ],
            'Desserts' => [
                ['name' => 'Brownie', 'price' => 4.99, 'description' => 'Chocolate brownie', 'is_featured' => false],
                ['name' => 'Ice Cream', 'price' => 3.99, 'description' => 'Vanilla, chocolate, strawberry', 'is_featured' => false],
            ],
            'Sides' => [
                ['name' => 'Fries', 'price' => 2.99, 'description' => 'Crispy fries', 'is_featured' => false],
                ['name' => 'Onion Rings', 'price' => 3.49, 'description' => 'Crispy onion rings', 'is_featured' => false],
            ],
        ];

        $sort = 0;
        foreach ($items as $catName => $list) {
            $category = Category::where('name', $catName)->first();
            if (!$category) continue;
            foreach ($list as $i) {
                Item::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'slug' => \Illuminate\Support\Str::slug($i['name']),
                    ],
                    [
                        'name' => $i['name'],
                        'description' => $i['description'],
                        'price' => $i['price'],
                        'is_available' => true,
                        'is_featured' => $i['is_featured'],
                        'sort_order' => ++$sort,
                    ]
                );
            }
        }
    }
}
