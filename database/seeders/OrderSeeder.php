<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use App\Models\Discount;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy tất cả users và products
        $users = User::all();
        $products = Product::where('active', 1)->get();
        $discounts = Discount::where('active', 1)->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Cần có users và products trước khi tạo orders!');
            return;
        }

        $statuses = ['pending', 'processing', 'shipping', 'delivered', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed'];
        $paymentMethods = ['COD', 'VNPAY', 'MOMO', 'BANK'];

        // Tạo 50 đơn hàng mẫu
        for ($i = 1; $i <= 50; $i++) {
            $user = $users->random();
            $status = $statuses[array_rand($statuses)];
            
            // Xác định payment_status dựa trên status
            if ($status === 'delivered') {
                $paymentStatus = 'paid';
            } elseif ($status === 'cancelled') {
                $paymentStatus = ['pending', 'failed'][array_rand(['pending', 'failed'])];
            } else {
                $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            }

            $order = Order::create([
                'user_id' => $user->id,
                'discount_id' => rand(0, 3) === 0 && $discounts->isNotEmpty() ? $discounts->random()->id : null,
                'order_code' => 'ORD' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'subtotal' => 0, // Sẽ tính sau
                'shipping_fee' => rand(0, 1) === 0 ? 0 : rand(20000, 50000),
                'discount' => 0, // Sẽ tính sau
                'status' => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'shipping_address' => $user->address ?? $this->generateAddress(),
                'notes' => rand(0, 2) === 0 ? $this->generateNote() : null,
                'created_at' => now()->subDays(rand(0, 90))->subHours(rand(0, 23)),
            ]);

            // Tạo 1-5 order items cho mỗi đơn hàng
            $numItems = rand(1, 5);
            $subtotal = 0;

            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $price = $product->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $product->variants()->exists() ? $product->variants->random()->id : null,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);

                $subtotal += $price * $quantity;
            }

            // Tính discount nếu có
            $discountAmount = 0;
            if ($order->discount_id) {
                $discount = $discounts->find($order->discount_id);
                if ($discount) {
                    if ($discount->type === 'percentage') {
                        $discountAmount = $subtotal * ($discount->percentage / 100);
                        if ($discount->max_discount && $discountAmount > $discount->max_discount) {
                            $discountAmount = $discount->max_discount;
                        }
                    } else {
                        $discountAmount = $discount->amount;
                    }
                }
            }

            // Cập nhật subtotal và discount
            $order->update([
                'subtotal' => $subtotal,
                'discount' => $discountAmount
            ]);
            // Total sẽ tự động tính bởi stored column: subtotal + shipping_fee - discount
        }

        $this->command->info('Đã tạo 50 đơn hàng mẫu thành công!');
    }

    private function generateAddress(): string
    {
        $streets = ['Lê Lợi', 'Nguyễn Huệ', 'Trần Hưng Đạo', 'Hai Bà Trưng', 'Điện Biên Phủ', 'Hoàng Văn Thụ'];
        $wards = ['Phường 1', 'Phường 2', 'Phường Bến Nghé', 'Phường Đa Kao', 'Phường Tân Định'];
        $districts = ['Quận 1', 'Quận 3', 'Quận 5', 'Quận 10', 'Quận Bình Thạnh'];
        $cities = ['TP.HCM', 'Hà Nội', 'Đà Nẵng', 'Cần Thơ'];

        return rand(1, 999) . ' ' . 
               $streets[array_rand($streets)] . ', ' . 
               $wards[array_rand($wards)] . ', ' . 
               $districts[array_rand($districts)] . ', ' . 
               $cities[array_rand($cities)];
    }

    private function generateNote(): string
    {
        $notes = [
            'Giao hàng giờ hành chính',
            'Gọi điện trước khi giao',
            'Giao tận tay, không gửi bảo vệ',
            'Kiểm tra kỹ hàng trước khi thanh toán',
            'Khách yêu cầu đóng gói cẩn thận',
            'Giao hàng buổi sáng',
            'Giao hàng sau 18h',
            'Không giao vào cuối tuần'
        ];

        return $notes[array_rand($notes)];
    }
}
