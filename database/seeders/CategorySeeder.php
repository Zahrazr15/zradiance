<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create([
            'cat_title' => 'Makeup',
        ]);

        Category::create([
            'cat_title' => 'Skincare',
        ]);

        Category::create([
            'cat_title' => 'Body Care',
        ]);

        Category::create([
            'cat_title' => 'Hair Care',
        ]);
    }
}