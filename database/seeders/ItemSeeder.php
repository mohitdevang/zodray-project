<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Laptop', 'price' => 999.99, 'description' => '15-inch notebook', 'is_active' => true],
            ['name' => 'Mouse', 'price' => 19.99, 'description' => 'Wireless mouse', 'is_active' => true],
            ['name' => 'Keyboard', 'price' => 49.99, 'description' => 'Mechanical keyboard', 'is_active' => true],
            ['name' => 'Headphones', 'price' => 79.99, 'description' => 'Over-ear headphones', 'is_active' => true],
        ];

        foreach ($items as $data) {
            Item::updateOrCreate(['name' => $data['name']], $data);
        }
    }
}


