<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discounts = [
            // Giảm phần trăm
            [
                'code' => 'WELCOME10',
                'type' => 'percentage',
                'value' => 10,
                'percentage' => 10,
                'amount' => null,
                'start_date' => '2025-01-01 00:00:00',
                'end_date' => '2025-12-31 23:59:59',
                'usage_limit' => 1000,
                'used_count' => rand(0, 100),
                'min_purchase' => 100000,
                'max_discount' => 500000,
                'description' => 'Giảm 10% cho đơn hàng đầu tiên',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'TECH20',
                'type' => 'percentage',
                'value' => 20,
                'percentage' => 20,
                'amount' => null,
                'start_date' => '2025-01-01 00:00:00',
                'end_date' => '2025-12-31 23:59:59',
                'usage_limit' => 500,
                'used_count' => rand(0, 150),
                'min_purchase' => 500000,
                'max_discount' => 1000000,
                'description' => 'Giảm 20% cho sản phẩm công nghệ',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'VIP30',
                'type' => 'percentage',
                'value' => 30,
                'percentage' => 30,
                'amount' => null,
                'start_date' => '2025-01-01 00:00:00',
                'end_date' => '2025-12-31 23:59:59',
                'usage_limit' => 100,
                'used_count' => rand(0, 50),
                'min_purchase' => 1000000,
                'max_discount' => 3000000,
                'description' => 'Giảm 30% cho khách hàng VIP',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Giảm tiền cố định
            [
                'code' => 'SAVE100K',
                'type' => 'amount',
                'value' => 100000,
                'percentage' => null,
                'amount' => 100000,
                'start_date' => '2025-01-01 00:00:00',
                'end_date' => '2025-12-31 23:59:59',
                'usage_limit' => 2000,
                'used_count' => rand(0, 200),
                'min_purchase' => 300000,
                'max_discount' => null,
                'description' => 'Giảm 100.000đ cho đơn hàng từ 300k',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'TECH500K',
                'type' => 'amount',
                'value' => 500000,
                'percentage' => null,
                'amount' => 500000,
                'start_date' => '2025-01-01 00:00:00',
                'end_date' => '2025-12-31 23:59:59',
                'usage_limit' => 500,
                'used_count' => rand(0, 100),
                'min_purchase' => 2000000,
                'max_discount' => null,
                'description' => 'Giảm 500.000đ cho đơn hàng từ 2 triệu',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'MEGA1M',
                'type' => 'amount',
                'value' => 1000000,
                'percentage' => null,
                'amount' => 1000000,
                'start_date' => '2025-01-01 00:00:00',
                'end_date' => '2025-12-31 23:59:59',
                'usage_limit' => 100,
                'used_count' => rand(0, 30),
                'min_purchase' => 5000000,
                'max_discount' => null,
                'description' => 'Giảm 1 triệu cho đơn hàng từ 5 triệu',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Mã Free Ship (Giảm tiền bằng phí ship - 30k)
            [
                'code' => 'FREESHIP',
                'type' => 'amount',
                'value' => 30000,
                'percentage' => null,
                'amount' => 30000,
                'start_date' => '2025-01-01 00:00:00',
                'end_date' => '2025-12-31 23:59:59',
                'usage_limit' => 5000,
                'used_count' => rand(0, 500),
                'min_purchase' => 200000,
                'max_discount' => null,
                'description' => 'Miễn phí vận chuyển cho đơn từ 200k',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SHIPVIP',
                'type' => 'amount',
                'value' => 30000,
                'percentage' => null,
                'amount' => 30000,
                'start_date' => '2025-01-01 00:00:00',
                'end_date' => '2025-12-31 23:59:59',
                'usage_limit' => 1000,
                'used_count' => rand(0, 100),
                'min_purchase' => 500000,
                'max_discount' => null,
                'description' => 'Miễn phí vận chuyển VIP cho đơn từ 500k',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Mã giảm giá đặc biệt
            [
                'code' => 'BLACKFRIDAY50',
                'type' => 'percentage',
                'value' => 50,
                'percentage' => 50,
                'amount' => null,
                'start_date' => '2025-11-25 00:00:00',
                'end_date' => '2025-11-30 23:59:59',
                'usage_limit' => 1000,
                'used_count' => rand(0, 500),
                'min_purchase' => 500000,
                'max_discount' => 5000000,
                'description' => 'Black Friday - Giảm 50% toàn bộ đơn hàng',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'NEWYEAR2M',
                'type' => 'amount',
                'value' => 2000000,
                'percentage' => null,
                'amount' => 2000000,
                'start_date' => '2025-12-25 00:00:00',
                'end_date' => '2026-01-05 23:59:59',
                'usage_limit' => 50,
                'used_count' => rand(0, 10),
                'min_purchase' => 10000000,
                'max_discount' => null,
                'description' => 'Năm mới - Giảm 2 triệu cho đơn từ 10 triệu',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SUMMER15',
                'type' => 'percentage',
                'value' => 15,
                'percentage' => 15,
                'amount' => null,
                'start_date' => '2025-06-01 00:00:00',
                'end_date' => '2025-08-31 23:59:59',
                'usage_limit' => 1500,
                'used_count' => rand(0, 300),
                'min_purchase' => 200000,
                'max_discount' => 800000,
                'description' => 'Khuyến mãi mùa hè - Giảm 15%',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'FLASHSALE',
                'type' => 'amount',
                'value' => 250000,
                'percentage' => null,
                'amount' => 250000,
                'start_date' => '2025-01-01 00:00:00',
                'end_date' => '2025-12-31 23:59:59',
                'usage_limit' => 3000,
                'used_count' => rand(0, 500),
                'min_purchase' => 500000,
                'max_discount' => null,
                'description' => 'Flash Sale - Giảm 250k cho đơn từ 500k',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert discounts và lấy IDs
        foreach ($discounts as $discount) {
            DB::table('discounts')->insert($discount);
        }

        // Lấy tất cả discount IDs vừa tạo
        $discountIds = DB::table('discounts')->pluck('id')->toArray();
        
        // Gán tất cả mã giảm giá cho user id = 1
        $userId = 1;
        $discountUserData = [];
        
        foreach ($discountIds as $discountId) {
            $discountUserData[] = [
                'discount_id' => $discountId,
                'user_id' => $userId,
                'used' => 0, // Chưa sử dụng
                'assigned_at' => now(),
                'used_at' => null,
            ];
        }
        
        DB::table('discount_user')->insert($discountUserData);
        
        $this->command->info('✅ Đã seed ' . count($discounts) . ' mã giảm giá!');
        $this->command->info('✅ Đã gán tất cả mã cho User ID = 1');
    }
}
