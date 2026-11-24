<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Skincare',
            'Makeup',
            'Hair Care',
            'Body Care',
            'Fragrance',
            'Sunscreen',
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['cat_title' => $category],
                ['cat_title' => $category]
            );
        }
    }
}