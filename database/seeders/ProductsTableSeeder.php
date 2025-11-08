<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('product_variants')->truncate();
        DB::table('products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ========== SMARTPHONES ==========
        
        // iPhone 15 Pro Max (Flagship - Đắt nhất)
        $iphone15ProMax = Product::create([
            'name' => 'iPhone 15 Pro Max',
            'description' => 'iPhone 15 Pro Max với chip A17 Pro, khung titan cao cấp, camera 48MP ProRAW, zoom quang học 5x, màn hình Super Retina XDR 6.7 inch ProMotion 120Hz. Thiết kế titan nhẹ và bền bỉ.',
            'image' => 'iphone-15-pro-max.jpg',
            'category_id' => 1, // Điện thoại
            'brand_id' => 1, // Apple
            'created_at' => now()->subDays(15),
            'updated_at' => now()->subDays(15),
        ]);

        $colors15ProMax = [
            ['name' => 'Titan Tự Nhiên', 'code' => '#8B8B8B'],
            ['name' => 'Titan Xanh', 'code' => '#2B5278'],
            ['name' => 'Titan Trắng', 'code' => '#E8E8E8'],
            ['name' => 'Titan Đen', 'code' => '#1C1C1C'],
        ];

        foreach ($colors15ProMax as $colorIndex => $color) {
            foreach (['256GB', '512GB', '1TB'] as $storageIndex => $storage) {
                $basePrice = [
                    '256GB' => 29990000,
                    '512GB' => 34990000,
                    '1TB' => 39990000,
                ][$storage];

                ProductVariant::create([
                    'product_id' => $iphone15ProMax->id,
                    'sku' => 'IP15PM-' . str_replace('GB', '', $storage) . '-C' . $colorIndex . 'S' . $storageIndex,
                    'attributes' => json_encode([
                        'color' => $color['name'],
                        'color_code' => $color['code'],
                        'storage' => $storage,
                        'ram' => '8GB',
                        'battery' => '4422mAh',
                    ]),
                    'price' => $basePrice,
                    'discount' => 5,
                    'stock' => rand(30, 80),
                ]);
            }
        }

        // Samsung Galaxy S24 Ultra (Flagship Android - Đắt)
        $s24Ultra = Product::create([
            'name' => 'Samsung Galaxy S24 Ultra',
            'description' => 'Galaxy S24 Ultra với bút S Pen tích hợp, camera zoom 100x Space Zoom, chip Snapdragon 8 Gen 3, màn hình Dynamic AMOLED 2X 6.8 inch QHD+, khung titan. Trí tuệ nhân tạo Galaxy AI.',
            'image' => 's24-ultra.jpg',
            'category_id' => 1,
            'brand_id' => 2, // Samsung
            'created_at' => now()->subDays(20),
            'updated_at' => now()->subDays(20),
        ]);

        $colorsS24 = [
            ['name' => 'Titan Xám', 'code' => '#4A4A4A'],
            ['name' => 'Titan Tím', 'code' => '#8B7BA8'],
            ['name' => 'Titan Vàng', 'code' => '#D4AF37'],
            ['name' => 'Titan Đen', 'code' => '#000000'],
        ];

        foreach ($colorsS24 as $colorIndex => $color) {
            foreach (['256GB', '512GB', '1TB'] as $storageIndex => $storage) {
                $basePrice = [
                    '256GB' => 27990000,
                    '512GB' => 31990000,
                    '1TB' => 36990000,
                ][$storage];

                ProductVariant::create([
                    'product_id' => $s24Ultra->id,
                    'sku' => 'S24U-' . str_replace('GB', '', $storage) . '-C' . $colorIndex . 'S' . $storageIndex,
                    'attributes' => json_encode([
                        'color' => $color['name'],
                        'color_code' => $color['code'],
                        'storage' => $storage,
                        'ram' => '12GB',
                        'battery' => '5000mAh',
                    ]),
                    'price' => $basePrice,
                    'discount' => 8,
                    'stock' => rand(40, 90),
                ]);
            }
        }

        // iPhone 15 (Tầm trung cao)
        $iphone15 = Product::create([
            'name' => 'iPhone 15',
            'description' => 'iPhone 15 với Dynamic Island, chip A16 Bionic, camera 48MP, USB-C, màn hình Super Retina XDR 6.1 inch. Thiết kế kính màu và khung nhôm hàng không vũ trụ.',
            'image' => 'iphone-15.jpg',
            'category_id' => 1,
            'brand_id' => 1,
            'created_at' => now()->subDays(25),
            'updated_at' => now()->subDays(25),
        ]);

        $colors15 = [
            ['name' => 'Đen', 'code' => '#000000'],
            ['name' => 'Xanh Dương', 'code' => '#2B4B8C'],
            ['name' => 'Hồng', 'code' => '#FFB6C1'],
            ['name' => 'Vàng', 'code' => '#FFD700'],
            ['name' => 'Xanh Lá', 'code' => '#90EE90'],
        ];

        foreach ($colors15 as $colorIndex => $color) {
            foreach (['128GB', '256GB', '512GB'] as $storageIndex => $storage) {
                $basePrice = [
                    '128GB' => 19990000,
                    '256GB' => 22990000,
                    '512GB' => 27990000,
                ][$storage];

                ProductVariant::create([
                    'product_id' => $iphone15->id,
                    'sku' => 'IP15-' . str_replace('GB', '', $storage) . '-C' . $colorIndex . 'S' . $storageIndex,
                    'attributes' => json_encode([
                        'color' => $color['name'],
                        'color_code' => $color['code'],
                        'storage' => $storage,
                        'ram' => '6GB',
                        'battery' => '3349mAh',
                    ]),
                    'price' => $basePrice,
                    'discount' => 7,
                    'stock' => rand(50, 100),
                ]);
            }
        }

        // Xiaomi 14 Ultra (Tầm trung cao)
        $xiaomi14Ultra = Product::create([
            'name' => 'Xiaomi 14 Ultra',
            'description' => 'Xiaomi 14 Ultra với camera Leica 50MP, zoom quang học 5x, chip Snapdragon 8 Gen 3, sạc nhanh 90W, màn hình AMOLED 6.73 inch 2K 120Hz. Camera đỉnh cao phân khúc.',
            'image' => 'xiaomi-14-ultra.jpg',
            'category_id' => 1,
            'brand_id' => 3, // Xiaomi
            'created_at' => now()->subDays(30),
            'updated_at' => now()->subDays(30),
        ]);

        $colorsXiaomi = [
            ['name' => 'Đen', 'code' => '#000000'],
            ['name' => 'Trắng', 'code' => '#FFFFFF'],
            ['name' => 'Xanh Dương', 'code' => '#1E3A8A'],
        ];

        foreach ($colorsXiaomi as $colorIndex => $color) {
            foreach (['256GB', '512GB'] as $storageIndex => $storage) {
                $basePrice = [
                    '256GB' => 21990000,
                    '512GB' => 24990000,
                ][$storage];

                ProductVariant::create([
                    'product_id' => $xiaomi14Ultra->id,
                    'sku' => 'X14U-' . str_replace('GB', '', $storage) . '-C' . $colorIndex . 'S' . $storageIndex,
                    'attributes' => json_encode([
                        'color' => $color['name'],
                        'color_code' => $color['code'],
                        'storage' => $storage,
                        'ram' => '16GB',
                        'battery' => '5000mAh',
                    ]),
                    'price' => $basePrice,
                    'discount' => 10,
                    'stock' => rand(40, 80),
                ]);
            }
        }

        // OPPO Reno11 5G (Tầm trung)
        $oppoReno11 = Product::create([
            'name' => 'OPPO Reno11 5G',
            'description' => 'OPPO Reno11 với camera chân dung 32MP, chip MediaTek Dimensity 7050, sạc nhanh SUPERVOOC 67W, màn hình AMOLED 6.7 inch 120Hz. Thiết kế mỏng nhẹ sang trọng.',
            'image' => 'oppo-reno11.jpg',
            'category_id' => 1,
            'brand_id' => 4, // OPPO
            'created_at' => now()->subDays(35),
            'updated_at' => now()->subDays(35),
        ]);

        $colorsOppo = [
            ['name' => 'Xanh Ngọc', 'code' => '#40E0D0'],
            ['name' => 'Tím Galaxy', 'code' => '#9370DB'],
            ['name' => 'Đen', 'code' => '#000000'],
        ];

        foreach ($colorsOppo as $colorIndex => $color) {
            foreach (['128GB', '256GB'] as $storageIndex => $storage) {
                $basePrice = [
                    '128GB' => 9990000,
                    '256GB' => 11990000,
                ][$storage];

                ProductVariant::create([
                    'product_id' => $oppoReno11->id,
                    'sku' => 'RN11-' . str_replace('GB', '', $storage) . '-C' . $colorIndex . 'S' . $storageIndex,
                    'attributes' => json_encode([
                        'color' => $color['name'],
                        'color_code' => $color['code'],
                        'storage' => $storage,
                        'ram' => '8GB',
                        'battery' => '5000mAh',
                    ]),
                    'price' => $basePrice,
                    'discount' => 12,
                    'stock' => rand(60, 120),
                ]);
            }
        }

        // Samsung Galaxy A55 5G (Phổ thông)
        $a55 = Product::create([
            'name' => 'Samsung Galaxy A55 5G',
            'description' => 'Galaxy A55 5G với camera 50MP OIS, chip Exynos 1480, pin 5000mAh, màn hình Super AMOLED 6.6 inch 120Hz. Khung kim loại cao cấp, chống nước IP67.',
            'image' => 'galaxy-a55.jpg',
            'category_id' => 1,
            'brand_id' => 2,
            'created_at' => now()->subDays(40),
            'updated_at' => now()->subDays(40),
        ]);

        $colorsA55 = [
            ['name' => 'Xanh Navy', 'code' => '#000080'],
            ['name' => 'Xanh Lá', 'code' => '#32CD32'],
            ['name' => 'Tím Lilac', 'code' => '#C8A2C8'],
        ];

        foreach ($colorsA55 as $colorIndex => $color) {
            foreach (['128GB', '256GB'] as $storageIndex => $storage) {
                $basePrice = [
                    '128GB' => 8990000,
                    '256GB' => 10490000,
                ][$storage];

                ProductVariant::create([
                    'product_id' => $a55->id,
                    'sku' => 'A55-' . str_replace('GB', '', $storage) . '-C' . $colorIndex . 'S' . $storageIndex,
                    'attributes' => json_encode([
                        'color' => $color['name'],
                        'color_code' => $color['code'],
                        'storage' => $storage,
                        'ram' => '8GB',
                        'battery' => '5000mAh',
                    ]),
                    'price' => $basePrice,
                    'discount' => 15,
                    'stock' => rand(80, 150),
                ]);
            }
        }

        // ========== LAPTOPS ==========

        // Dell XPS 15 9530 (Premium - Đắt nhất)
        $dellXPS15 = Product::create([
            'name' => 'Dell XPS 15 9530',
            'description' => 'Dell XPS 15 với Intel Core i9-13900H, RTX 4070, màn hình 15.6" OLED 3.5K, RAM 32GB, SSD 1TB. Thiết kế InfinityEdge, vỏ nhôm CNC cao cấp, pin 86Wh.',
            'image' => 'dell-xps-15.jpg',
            'category_id' => 2, // Laptop
            'brand_id' => 5, // Dell
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        $colorsDell = [
            ['name' => 'Platinum Silver', 'code' => '#C0C0C0'],
            ['name' => 'Graphite', 'code' => '#383838'],
        ];

        foreach ($colorsDell as $colorIndex => $color) {
            foreach ([
                ['cpu' => 'i9-13900H', 'ram' => '32GB', 'storage' => '1TB SSD', 'price' => 59990000],
                ['cpu' => 'i7-13700H', 'ram' => '16GB', 'storage' => '512GB SSD', 'price' => 45990000],
            ] as $specIndex => $spec) {
                ProductVariant::create([
                    'product_id' => $dellXPS15->id,
                    'sku' => 'XPS15-C' . $colorIndex . '-S' . $specIndex,
                    'attributes' => json_encode([
                        'color' => $color['name'],
                        'color_code' => $color['code'],
                        'cpu' => $spec['cpu'],
                        'ram' => $spec['ram'],
                        'storage' => $spec['storage'],
                        'screen_size' => '15.6" OLED 3.5K',
                    ]),
                    'price' => $spec['price'],
                    'discount' => 5,
                    'stock' => rand(15, 30),
                ]);
            }
        }

        // Asus ROG Zephyrus G16 (Gaming - Đắt)
        $rogG16 = Product::create([
            'name' => 'Asus ROG Zephyrus G16',
            'description' => 'ROG Zephyrus G16 với Intel Core i9-14900HS, RTX 4080, màn hình 16" 2.5K 240Hz, RAM 32GB DDR5, SSD 1TB. Thiết kế mỏng nhẹ 1.95kg, tản nhiệt ROG Intelligent Cooling.',
            'image' => 'rog-zephyrus-g16.jpg',
            'category_id' => 2,
            'brand_id' => 7, // Asus
            'created_at' => now()->subDays(18),
            'updated_at' => now()->subDays(18),
        ]);

        $colorsROG = [
            ['name' => 'Eclipse Gray', 'code' => '#2F2F2F'],
        ];

        foreach ($colorsROG as $colorIndex => $color) {
            foreach ([
                ['cpu' => 'i9-14900HS', 'ram' => '32GB', 'storage' => '1TB SSD', 'price' => 69990000],
                ['cpu' => 'i7-14650HX', 'ram' => '16GB', 'storage' => '512GB SSD', 'price' => 52990000],
            ] as $specIndex => $spec) {
                ProductVariant::create([
                    'product_id' => $rogG16->id,
                    'sku' => 'ROG-G16-C' . $colorIndex . '-S' . $specIndex,
                    'attributes' => json_encode([
                        'color' => $color['name'],
                        'color_code' => $color['code'],
                        'cpu' => $spec['cpu'],
                        'ram' => $spec['ram'],
                        'storage' => $spec['storage'],
                        'screen_size' => '16" 2.5K 240Hz',
                    ]),
                    'price' => $spec['price'],
                    'discount' => 7,
                    'stock' => rand(10, 25),
                ]);
            }
        }

        // HP Pavilion 15 (Tầm trung)
        $hpPavilion15 = Product::create([
            'name' => 'HP Pavilion 15',
            'description' => 'HP Pavilion 15 với Intel Core i5-1335U, Intel Iris Xe Graphics, màn hình 15.6" FHD IPS, RAM 16GB, SSD 512GB. Laptop văn phòng đa năng, pin 41Wh.',
            'image' => 'hp-pavilion-15.jpg',
            'category_id' => 2,
            'brand_id' => 6, // HP
            'created_at' => now()->subDays(22),
            'updated_at' => now()->subDays(22),
        ]);

        $colorsHP = [
            ['name' => 'Natural Silver', 'code' => '#D3D3D3'],
            ['name' => 'Warm Gold', 'code' => '#FFD700'],
        ];

        foreach ($colorsHP as $colorIndex => $color) {
            foreach ([
                ['cpu' => 'i5-1335U', 'ram' => '16GB', 'storage' => '512GB SSD', 'price' => 16990000],
                ['cpu' => 'i5-1335U', 'ram' => '8GB', 'storage' => '256GB SSD', 'price' => 13990000],
            ] as $specIndex => $spec) {
                ProductVariant::create([
                    'product_id' => $hpPavilion15->id,
                    'sku' => 'HPP15-C' . $colorIndex . '-S' . $specIndex,
                    'attributes' => json_encode([
                        'color' => $color['name'],
                        'color_code' => $color['code'],
                        'cpu' => $spec['cpu'],
                        'ram' => $spec['ram'],
                        'storage' => $spec['storage'],
                        'screen_size' => '15.6" FHD IPS',
                    ]),
                    'price' => $spec['price'],
                    'discount' => 10,
                    'stock' => rand(30, 60),
                ]);
            }
        }

        // Lenovo IdeaPad Slim 5 (Phổ thông)
        $ideaPad5 = Product::create([
            'name' => 'Lenovo IdeaPad Slim 5',
            'description' => 'IdeaPad Slim 5 với AMD Ryzen 5 7530U, AMD Radeon Graphics, màn hình 14" FHD IPS, RAM 16GB, SSD 512GB. Thiết kế mỏng nhẹ 1.46kg, pin 56.6Wh.',
            'image' => 'ideapad-slim-5.jpg',
            'category_id' => 2,
            'brand_id' => 8, // Lenovo
            'created_at' => now()->subDays(28),
            'updated_at' => now()->subDays(28),
        ]);

        $colorsLenovo = [
            ['name' => 'Cloud Grey', 'code' => '#B0B0B0'],
            ['name' => 'Abyss Blue', 'code' => '#1E3A5F'],
        ];

        foreach ($colorsLenovo as $colorIndex => $color) {
            foreach ([
                ['cpu' => 'Ryzen 5 7530U', 'ram' => '16GB', 'storage' => '512GB SSD', 'price' => 14990000],
                ['cpu' => 'Ryzen 5 7530U', 'ram' => '8GB', 'storage' => '256GB SSD', 'price' => 11990000],
            ] as $specIndex => $spec) {
                ProductVariant::create([
                    'product_id' => $ideaPad5->id,
                    'sku' => 'IP5-C' . $colorIndex . '-S' . $specIndex,
                    'attributes' => json_encode([
                        'color' => $color['name'],
                        'color_code' => $color['code'],
                        'cpu' => $spec['cpu'],
                        'ram' => $spec['ram'],
                        'storage' => $spec['storage'],
                        'screen_size' => '14" FHD IPS',
                    ]),
                    'price' => $spec['price'],
                    'discount' => 12,
                    'stock' => rand(40, 80),
                ]);
            }
        }

        // ========== TAI NGHE ==========

        // Sony WH-1000XM5 (Premium)
        $sonyWH1000XM5 = Product::create([
            'name' => 'Sony WH-1000XM5',
            'description' => 'Sony WH-1000XM5 với chống ồn tốt nhất thị trường, 8 microphone, thời lượng pin 30 giờ, sạc nhanh, LDAC Hi-Res Audio, thiết kế mới mỏng nhẹ hơn.',
            'image' => 'sony-wh1000xm5.jpg',
            'category_id' => 3, // Tai nghe
            'brand_id' => 9, // Sony
            'created_at' => now()->subDays(12),
            'updated_at' => now()->subDays(12),
        ]);

        $colorsSony = [
            ['name' => 'Đen', 'code' => '#000000'],
            ['name' => 'Bạc', 'code' => '#C0C0C0'],
        ];

        foreach ($colorsSony as $colorIndex => $color) {
            ProductVariant::create([
                'product_id' => $sonyWH1000XM5->id,
                'sku' => 'SONY-XM5-C' . $colorIndex,
                'attributes' => json_encode([
                    'color' => $color['name'],
                    'color_code' => $color['code'],
                    'connection' => 'Bluetooth 5.2',
                    'noise_cancellation' => 'Có',
                    'battery_life' => '30 giờ',
                ]),
                'price' => 8990000,
                'discount' => 8,
                'stock' => rand(50, 100),
            ]);
        }

        // Apple AirPods Pro 2 (Premium)
        $airPodsPro2 = Product::create([
            'name' => 'Apple AirPods Pro 2 USB-C',
            'description' => 'AirPods Pro 2 với chip H2, chống ồn chủ động 2x tốt hơn, Adaptive Audio, âm thanh không gian, cổng sạc USB-C, khả năng chống nước IPX4.',
            'image' => 'airpods-pro-2.jpg',
            'category_id' => 3,
            'brand_id' => 1, // Apple
            'created_at' => now()->subDays(16),
            'updated_at' => now()->subDays(16),
        ]);

        $colorsAirPods = [
            ['name' => 'Trắng', 'code' => '#FFFFFF'],
        ];

        foreach ($colorsAirPods as $colorIndex => $color) {
            ProductVariant::create([
                'product_id' => $airPodsPro2->id,
                'sku' => 'APP2-USBC-C' . $colorIndex,
                'attributes' => json_encode([
                    'color' => $color['name'],
                    'color_code' => $color['code'],
                    'connection' => 'Bluetooth 5.3',
                    'noise_cancellation' => 'Có',
                    'battery_life' => '6 giờ (30 giờ với case)',
                ]),
                'price' => 6490000,
                'discount' => 5,
                'stock' => rand(60, 120),
            ]);
        }

        // JBL Tune 770NC (Tầm trung)
        $jblTune770 = Product::create([
            'name' => 'JBL Tune 770NC',
            'description' => 'JBL Tune 770NC với chống ồn chủ động, JBL Pure Bass Sound, thời lượng pin 70 giờ, sạc nhanh, kết nối đa điểm, thiết kế gấp gọn tiện lợi.',
            'image' => 'jbl-tune-770nc.jpg',
            'category_id' => 3,
            'brand_id' => 10, // JBL
            'created_at' => now()->subDays(24),
            'updated_at' => now()->subDays(24),
        ]);

        $colorsJBL = [
            ['name' => 'Đen', 'code' => '#000000'],
            ['name' => 'Xanh Dương', 'code' => '#0000FF'],
            ['name' => 'Trắng', 'code' => '#FFFFFF'],
        ];

        foreach ($colorsJBL as $colorIndex => $color) {
            ProductVariant::create([
                'product_id' => $jblTune770->id,
                'sku' => 'JBL-770NC-C' . $colorIndex,
                'attributes' => json_encode([
                    'color' => $color['name'],
                    'color_code' => $color['code'],
                    'connection' => 'Bluetooth 5.3',
                    'noise_cancellation' => 'Có',
                    'battery_life' => '70 giờ',
                ]),
                'price' => 2490000,
                'discount' => 15,
                'stock' => rand(80, 150),
            ]);
        }

        // Samsung Galaxy Buds2 Pro (Tầm trung)
        $buds2Pro = Product::create([
            'name' => 'Samsung Galaxy Buds2 Pro',
            'description' => 'Galaxy Buds2 Pro với chống ồn thông minh, Hi-Fi 24bit, âm thanh không gian 360 Audio, thiết kế nhỏ gọn thoải mái, kết nối đa thiết bị.',
            'image' => 'buds2-pro.jpg',
            'category_id' => 3,
            'brand_id' => 2, // Samsung
            'created_at' => now()->subDays(32),
            'updated_at' => now()->subDays(32),
        ]);

        $colorsBuds = [
            ['name' => 'Tím Graphite', 'code' => '#4B0082'],
            ['name' => 'Trắng', 'code' => '#FFFFFF'],
            ['name' => 'Bạc Bora', 'code' => '#E6E6FA'],
        ];

        foreach ($colorsBuds as $colorIndex => $color) {
            ProductVariant::create([
                'product_id' => $buds2Pro->id,
                'sku' => 'BUDS2PRO-C' . $colorIndex,
                'attributes' => json_encode([
                    'color' => $color['name'],
                    'color_code' => $color['code'],
                    'connection' => 'Bluetooth 5.3',
                    'noise_cancellation' => 'Có',
                    'battery_life' => '5 giờ (18 giờ với case)',
                ]),
                'price' => 3990000,
                'discount' => 18,
                'stock' => rand(70, 130),
            ]);
        }

        // ========== CHUỘT ==========

        // Logitech G Pro X Superlight 2 (Premium Gaming)
        $gProSuperlight2 = Product::create([
            'name' => 'Logitech G Pro X Superlight 2',
            'description' => 'G Pro X Superlight 2 với sensor HERO 2, 32K DPI, trọng lượng chỉ 60g, pin 95 giờ, switch quang học, thiết kế đối xứng cho pro gamers.',
            'image' => 'gpro-superlight2.jpg',
            'category_id' => 4, // Chuột
            'brand_id' => 11, // Logitech
            'created_at' => now()->subDays(8),
            'updated_at' => now()->subDays(8),
        ]);

        $colorsGPro = [
            ['name' => 'Đen', 'code' => '#000000'],
            ['name' => 'Trắng', 'code' => '#FFFFFF'],
            ['name' => 'Hồng', 'code' => '#FFB6C1'],
        ];

        foreach ($colorsGPro as $colorIndex => $color) {
            ProductVariant::create([
                'product_id' => $gProSuperlight2->id,
                'sku' => 'GPRO-SL2-C' . $colorIndex,
                'attributes' => json_encode([
                    'color' => $color['name'],
                    'color_code' => $color['code'],
                    'connection' => 'Wireless 2.4GHz',
                    'dpi' => '32000 DPI',
                    'sensor' => 'HERO 2',
                ]),
                'price' => 3990000,
                'discount' => 5,
                'stock' => rand(40, 80),
            ]);
        }

        // Razer DeathAdder V3 Pro (Premium Gaming)
        $deathAdderV3 = Product::create([
            'name' => 'Razer DeathAdder V3 Pro',
            'description' => 'DeathAdder V3 Pro với Focus Pro 30K sensor, switch quang học Gen-3, 90 giờ pin, trọng lượng 63g, thiết kế ergonomic huyền thoại cho tay phải.',
            'image' => 'deathadder-v3-pro.jpg',
            'category_id' => 4,
            'brand_id' => 12, // Razer
            'created_at' => now()->subDays(14),
            'updated_at' => now()->subDays(14),
        ]);

        $colorsRazer = [
            ['name' => 'Đen', 'code' => '#000000'],
            ['name' => 'Trắng', 'code' => '#FFFFFF'],
        ];

        foreach ($colorsRazer as $colorIndex => $color) {
            ProductVariant::create([
                'product_id' => $deathAdderV3->id,
                'sku' => 'DAV3PRO-C' . $colorIndex,
                'attributes' => json_encode([
                    'color' => $color['name'],
                    'color_code' => $color['code'],
                    'connection' => 'Wireless 2.4GHz + Bluetooth',
                    'dpi' => '30000 DPI',
                    'sensor' => 'Focus Pro 30K',
                ]),
                'price' => 3590000,
                'discount' => 8,
                'stock' => rand(35, 70),
            ]);
        }

        // Logitech G502 X (Tầm trung)
        $g502x = Product::create([
            'name' => 'Logitech G502 X',
            'description' => 'G502 X với sensor HERO 25K, switch quang học LIGHTFORCE, 13 phím có thể tùy chỉnh, scroll wheel kép, thiết kế cải tiến nhẹ hơn.',
            'image' => 'g502x.jpg',
            'category_id' => 4,
            'brand_id' => 11,
            'created_at' => now()->subDays(20),
            'updated_at' => now()->subDays(20),
        ]);

        $colorsG502 = [
            ['name' => 'Đen', 'code' => '#000000'],
            ['name' => 'Trắng', 'code' => '#FFFFFF'],
        ];

        foreach ($colorsG502 as $colorIndex => $color) {
            ProductVariant::create([
                'product_id' => $g502x->id,
                'sku' => 'G502X-C' . $colorIndex,
                'attributes' => json_encode([
                    'color' => $color['name'],
                    'color_code' => $color['code'],
                    'connection' => 'Có dây USB',
                    'dpi' => '25600 DPI',
                    'sensor' => 'HERO 25K',
                ]),
                'price' => 1790000,
                'discount' => 10,
                'stock' => rand(60, 120),
            ]);
        }

        // SteelSeries Rival 3 (Phổ thông)
        $rival3 = Product::create([
            'name' => 'SteelSeries Rival 3',
            'description' => 'Rival 3 với TrueMove Core sensor, 6 nút có thể lập trình, RGB Prism, thiết kế nhẹ 77g, tối ưu cho mọi game thủ.',
            'image' => 'rival-3.jpg',
            'category_id' => 4,
            'brand_id' => 14, // SteelSeries
            'created_at' => now()->subDays(26),
            'updated_at' => now()->subDays(26),
        ]);

        $colorsSteel = [
            ['name' => 'Đen', 'code' => '#000000'],
        ];

        foreach ($colorsSteel as $colorIndex => $color) {
            ProductVariant::create([
                'product_id' => $rival3->id,
                'sku' => 'RIVAL3-C' . $colorIndex,
                'attributes' => json_encode([
                    'color' => $color['name'],
                    'color_code' => $color['code'],
                    'connection' => 'Có dây USB',
                    'dpi' => '8500 DPI',
                    'sensor' => 'TrueMove Core',
                ]),
                'price' => 590000,
                'discount' => 15,
                'stock' => rand(100, 200),
            ]);
        }

        // ========== BÀN PHÍM ==========

        // Corsair K70 RGB Pro (Premium)
        $k70Pro = Product::create([
            'name' => 'Corsair K70 RGB Pro',
            'description' => 'K70 RGB Pro với switch Cherry MX, khung nhôm chắc chắn, RGB per-key, media controls, wrist rest tháo rời, polling rate 1000Hz.',
            'image' => 'k70-rgb-pro.jpg',
            'category_id' => 5, // Bàn phím
            'brand_id' => 13, // Corsair
            'created_at' => now()->subDays(9),
            'updated_at' => now()->subDays(9),
        ]);

        $switchTypes = ['Cherry MX Red', 'Cherry MX Brown', 'Cherry MX Blue'];
        foreach ($switchTypes as $switchIndex => $switch) {
            ProductVariant::create([
                'product_id' => $k70Pro->id,
                'sku' => 'K70PRO-S' . $switchIndex,
                'attributes' => json_encode([
                    'color' => 'Đen',
                    'color_code' => '#000000',
                    'switch_type' => $switch,
                    'layout' => 'Full-size (100%)',
                    'connection' => 'Có dây USB-C',
                ]),
                'price' => 3990000,
                'discount' => 7,
                'stock' => rand(30, 60),
            ]);
        }

        // Razer BlackWidow V4 Pro (Premium Gaming)
        $blackWidowV4 = Product::create([
            'name' => 'Razer BlackWidow V4 Pro',
            'description' => 'BlackWidow V4 Pro với Razer Green/Yellow switch, Command Dial, 8 macro keys, RGB Chroma, wrist rest plush, polling rate 1000Hz.',
            'image' => 'blackwidow-v4-pro.jpg',
            'category_id' => 5,
            'brand_id' => 12, // Razer
            'created_at' => now()->subDays(13),
            'updated_at' => now()->subDays(13),
        ]);

        $razerSwitches = ['Razer Green', 'Razer Yellow'];
        foreach ($razerSwitches as $switchIndex => $switch) {
            ProductVariant::create([
                'product_id' => $blackWidowV4->id,
                'sku' => 'BWV4PRO-S' . $switchIndex,
                'attributes' => json_encode([
                    'color' => 'Đen',
                    'color_code' => '#000000',
                    'switch_type' => $switch,
                    'layout' => 'Full-size (100%)',
                    'connection' => 'Có dây USB + Wireless 2.4GHz',
                ]),
                'price' => 5490000,
                'discount' => 10,
                'stock' => rand(25, 50),
            ]);
        }

        // Logitech G Pro X TKL (Tầm trung)
        $gProTKL = Product::create([
            'name' => 'Logitech G Pro X TKL',
            'description' => 'G Pro X TKL với switch GX hot-swappable, thiết kế tenkeyless, RGB LIGHTSYNC, khung nhôm, dây cáp tháo rời, tối ưu cho esports.',
            'image' => 'gpro-x-tkl.jpg',
            'category_id' => 5,
            'brand_id' => 11, // Logitech
            'created_at' => now()->subDays(19),
            'updated_at' => now()->subDays(19),
        ]);

        $gxSwitches = ['GX Blue', 'GX Brown', 'GX Red'];
        foreach ($gxSwitches as $switchIndex => $switch) {
            ProductVariant::create([
                'product_id' => $gProTKL->id,
                'sku' => 'GPROTKL-S' . $switchIndex,
                'attributes' => json_encode([
                    'color' => 'Đen',
                    'color_code' => '#000000',
                    'switch_type' => $switch,
                    'layout' => 'TKL (80%)',
                    'connection' => 'Có dây USB',
                ]),
                'price' => 2990000,
                'discount' => 12,
                'stock' => rand(40, 80),
            ]);
        }

        // SteelSeries Apex 3 (Phổ thông)
        $apex3 = Product::create([
            'name' => 'SteelSeries Apex 3',
            'description' => 'Apex 3 với switch membrane whisper-quiet, RGB 10-zone, IP32 chống nước, media controls, wrist rest자석, thiết kế bền bỉ.',
            'image' => 'apex-3.jpg',
            'category_id' => 5,
            'brand_id' => 14, // SteelSeries
            'created_at' => now()->subDays(27),
            'updated_at' => now()->subDays(27),
        ]);

        ProductVariant::create([
            'product_id' => $apex3->id,
            'sku' => 'APEX3-BK',
            'attributes' => json_encode([
                'color' => 'Đen',
                'color_code' => '#000000',
                'switch_type' => 'Membrane',
                'layout' => 'Full-size (100%)',
                'connection' => 'Có dây USB',
            ]),
            'price' => 1290000,
            'discount' => 18,
            'stock' => rand(80, 150),
        ]);

        // Cập nhật giá và views ngẫu nhiên cho tất cả products
        $products = Product::with('variants')->get();
        foreach ($products as $product) {
            $updateData = ['views' => rand(1, 1000)];
            
            if ($product->variants->isNotEmpty()) {
                $minPrice = $product->variants->min('price');
                $updateData['price'] = $minPrice;
            }
            
            $product->update($updateData);
        }

        echo "✅ Seeded successfully!\n";
        echo "- 6 Smartphones (từ 8.9M - 40M)\n";
        echo "- 4 Laptops (từ 11.9M - 70M)\n";
        echo "- 4 Tai nghe (từ 2.5M - 9M)\n";
        echo "- 4 Chuột (từ 590K - 4M)\n";
        echo "- 4 Bàn phím (từ 1.3M - 5.5M)\n";
        echo "Total: 22 products with diverse variants!\n";
        echo "Updated prices and random views (1-1000) for all products!\n";
    }
}
