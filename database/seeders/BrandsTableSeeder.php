<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;

class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('brands')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $brands = [
            // Smartphone brands
            [
                'name' => 'Apple',
                'description' => 'Thương hiệu công nghệ hàng đầu thế giới từ Mỹ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Samsung',
                'description' => 'Tập đoàn điện tử hàng đầu Hàn Quốc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Xiaomi',
                'description' => 'Thương hiệu công nghệ nổi tiếng Trung Quốc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'OPPO',
                'description' => 'Thương hiệu smartphone phổ biến từ Trung Quốc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Laptop brands
            [
                'name' => 'Dell',
                'description' => 'Thương hiệu máy tính hàng đầu từ Mỹ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HP',
                'description' => 'Hewlett-Packard - Thương hiệu máy tính uy tín',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Asus',
                'description' => 'Thương hiệu laptop và gaming gear từ Đài Loan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lenovo',
                'description' => 'Thương hiệu máy tính lớn nhất Trung Quốc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Audio brands
            [
                'name' => 'Sony',
                'description' => 'Thương hiệu điện tử hàng đầu Nhật Bản',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'JBL',
                'description' => 'Thương hiệu âm thanh chuyên nghiệp từ Mỹ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Gaming peripherals
            [
                'name' => 'Logitech',
                'description' => 'Thương hiệu thiết bị ngoại vi hàng đầu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Razer',
                'description' => 'Thương hiệu gaming gear cao cấp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Corsair',
                'description' => 'Thương hiệu gaming và PC components',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'SteelSeries',
                'description' => 'Thương hiệu gaming peripherals chuyên nghiệp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Brand::insert($brands);
    }
}
