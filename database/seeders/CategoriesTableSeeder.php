<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            [
                'name' => 'Điện thoại',
                'description' => 'Smartphone, điện thoại di động các hãng Apple, Samsung, Xiaomi, OPPO, Vivo...',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laptop',
                'description' => 'Laptop văn phòng, gaming, đồ họa từ các thương hiệu Dell, HP, Asus, Acer, Lenovo...',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tai nghe',
                'description' => 'Tai nghe bluetooth, có dây, gaming, chống ồn từ Sony, Apple, JBL, Samsung...',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chuột',
                'description' => 'Chuột gaming, văn phòng, không dây từ Logitech, Razer, SteelSeries...',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bàn phím',
                'description' => 'Bàn phím cơ, gaming, văn phòng từ Logitech, Razer, Corsair, Keychron...',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Category::insert($categories);
    }
}
