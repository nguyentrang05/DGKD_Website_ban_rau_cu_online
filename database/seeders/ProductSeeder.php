<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::all();

        $products = [
            // Rau củ quả
            [
                'name' => 'Cà rốt',
                'description' => 'Cà rốt tươi ngon, giàu vitamin A',
                'price' => 15000,
                'stock_quantity' => 100,
                'unit' => 'kg',
                'image_url' => 'https://example.com/images/carrot.jpg',
                'category_id' => 1
            ],
            [
                'name' => 'Bắp cải',
                'description' => 'Bắp cải xanh tươi, không thuốc trừ sâu',
                'price' => 12000,
                'stock_quantity' => 80,
                'unit' => 'kg',
                'image_url' => 'https://example.com/images/cabbage.jpg',
                'category_id' => 1
            ],

            // Trái cây
            [
                'name' => 'Táo Fuji',
                'description' => 'Táo Fuji nhập khẩu từ New Zealand',
                'price' => 65000,
                'stock_quantity' => 50,
                'unit' => 'kg',
                'image_url' => 'https://example.com/images/apple.jpg',
                'category_id' => 2
            ],
            [
                'name' => 'Chuối',
                'description' => 'Chuối tiêu chín tự nhiên',
                'price' => 25000,
                'stock_quantity' => 70,
                'unit' => 'nải',
                'image_url' => 'https://example.com/images/banana.jpg',
                'category_id' => 2
            ],

            // Thịt tươi
            [
                'name' => 'Thịt heo ba rọi',
                'description' => 'Thịt heo ba rọi tươi ngon, thịt mềm',
                'price' => 120000,
                'stock_quantity' => 30,
                'unit' => 'kg',
                'image_url' => 'https://example.com/images/pork.jpg',
                'category_id' => 3
            ],
            [
                'name' => 'Ức gà',
                'description' => 'Ức gà không da, giàu protein',
                'price' => 85000,
                'stock_quantity' => 40,
                'unit' => 'kg',
                'image_url' => 'https://example.com/images/chicken.jpg',
                'category_id' => 3
            ],

            // Hải sản
            [
                'name' => 'Tôm sú',
                'description' => 'Tôm sú tươi sống size lớn',
                'price' => 250000,
                'stock_quantity' => 20,
                'unit' => 'kg',
                'image_url' => 'https://example.com/images/shrimp.jpg',
                'category_id' => 4
            ],
            [
                'name' => 'Cá hồi',
                'description' => 'Cá hồi phi lê nhập khẩu từ Na Uy',
                'price' => 320000,
                'stock_quantity' => 15,
                'unit' => 'kg',
                'image_url' => 'https://example.com/images/salmon.jpg',
                'category_id' => 4
            ],

            // Thực phẩm khô
            [
                'name' => 'Gạo ST25',
                'description' => 'Gạo ST25 thơm ngon, dẻo',
                'price' => 35000,
                'stock_quantity' => 200,
                'unit' => 'kg',
                'image_url' => 'https://example.com/images/rice.jpg',
                'category_id' => 5
            ],
            [
                'name' => 'Mì gói Hảo Hảo',
                'description' => 'Mì gói Hảo Hảo vị tôm chua cay',
                'price' => 3500,
                'stock_quantity' => 500,
                'unit' => 'gói',
                'image_url' => 'https://example.com/images/noodle.jpg',
                'category_id' => 5
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => $product['description'],
                'price' => $product['price'],
                'stock_quantity' => $product['stock_quantity'],
                'unit' => $product['unit'],
                'image_url' => $product['image_url'],
                'category_id' => $product['category_id'],
            ]);
        }

        
    }
}
