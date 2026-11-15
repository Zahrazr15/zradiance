<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'brand' => 'Maybelline',
            'name' => 'SuperStay Matte Ink',
            'description' => 'Lip cream dengan hasil matte dan tahan lama.',
            'price' => 129000,
            'stock' => 20,
            'image' => null,
        ]);

        Product::create([
            'brand' => 'Wardah',
            'name' => 'Colorfit Velvet Matte Lip Mousse',
            'description' => 'Lip mousse dengan tekstur ringan dan hasil velvet matte.',
            'price' => 75000,
            'stock' => 25,
            'image' => null,
        ]);

        Product::create([
            'brand' => 'Emina',
            'name' => 'Creamatte Lip Cream',
            'description' => 'Lip cream ringan untuk penggunaan sehari-hari.',
            'price' => 55000,
            'stock' => 30,
            'image' => null,
        ]);

        Product::create([
            'brand' => 'Skintific',
            'name' => 'Perfect Stay Velvet Matte Cushion',
            'description' => 'Cushion dengan coverage yang nyaman untuk aktivitas sehari-hari.',
            'price' => 179000,
            'stock' => 15,
            'image' => null,
        ]);

        Product::create([
            'brand' => 'The Originote',
            'name' => 'Hyalucera Moisturizer',
            'description' => 'Moisturizer dengan kandungan hyaluronic acid dan ceramide.',
            'price' => 65000,
            'stock' => 18,
            'image' => null,
        ]);
    }
}