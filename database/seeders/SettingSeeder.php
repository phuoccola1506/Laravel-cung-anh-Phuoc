<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'TechShop',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Tên cửa hàng',
                'description' => 'Tên hiển thị của website'
            ],
            [
                'key' => 'site_description',
                'value' => 'Cửa hàng công nghệ hàng đầu Việt Nam',
                'type' => 'textarea',
                'group' => 'general',
                'label' => 'Mô tả website',
                'description' => 'Mô tả ngắn về cửa hàng'
            ],
            [
                'key' => 'site_logo',
                'value' => 'logo.png',
                'type' => 'image',
                'group' => 'general',
                'label' => 'Logo',
                'description' => 'Logo của website'
            ],
            [
                'key' => 'site_favicon',
                'value' => 'favicon.ico',
                'type' => 'image',
                'group' => 'general',
                'label' => 'Favicon',
                'description' => 'Icon hiển thị trên tab trình duyệt'
            ],

            // Contact Information
            [
                'key' => 'contact_email',
                'value' => 'support@techshop.vn',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Email liên hệ',
                'description' => 'Email hỗ trợ khách hàng'
            ],
            [
                'key' => 'contact_phone',
                'value' => '1800-1234',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Số điện thoại',
                'description' => 'Hotline hỗ trợ'
            ],
            [
                'key' => 'contact_address',
                'value' => '123 Nguyễn Văn Linh, Q.7, TP.HCM',
                'type' => 'textarea',
                'group' => 'contact',
                'label' => 'Địa chỉ',
                'description' => 'Địa chỉ cửa hàng'
            ],
            [
                'key' => 'contact_working_hours',
                'value' => 'T2-CN: 8h - 22h',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Giờ làm việc',
                'description' => 'Thời gian mở cửa'
            ],

            // Social Media
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/techshop',
                'type' => 'text',
                'group' => 'social',
                'label' => 'Facebook',
                'description' => 'Link Facebook fanpage'
            ],
            [
                'key' => 'social_youtube',
                'value' => 'https://youtube.com/@techshop',
                'type' => 'text',
                'group' => 'social',
                'label' => 'YouTube',
                'description' => 'Link kênh YouTube'
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/techshop',
                'type' => 'text',
                'group' => 'social',
                'label' => 'Instagram',
                'description' => 'Link Instagram'
            ],
            [
                'key' => 'social_tiktok',
                'value' => 'https://tiktok.com/@techshop',
                'type' => 'text',
                'group' => 'social',
                'label' => 'TikTok',
                'description' => 'Link TikTok'
            ],

            // Payment Methods
            [
                'key' => 'payment_cod',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'COD - Thanh toán khi nhận hàng',
                'description' => 'Kích hoạt thanh toán COD'
            ],
            [
                'key' => 'payment_bank',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'Chuyển khoản ngân hàng',
                'description' => 'Kích hoạt chuyển khoản ngân hàng'
            ],
            [
                'key' => 'payment_vnpay',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'VNPay',
                'description' => 'Kích hoạt thanh toán VNPay'
            ],
            [
                'key' => 'payment_momo',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'Ví MoMo',
                'description' => 'Kích hoạt thanh toán MoMo'
            ],

            // Shipping
            [
                'key' => 'shipping_fee',
                'value' => '100000',
                'type' => 'number',
                'group' => 'shipping',
                'label' => 'Phí vận chuyển',
                'description' => 'Phí vận chuyển mặc định'
            ],
            [
                'key' => 'shipping_free_threshold',
                'value' => '50000000',
                'type' => 'number',
                'group' => 'shipping',
                'label' => 'Miễn phí vận chuyển khi đơn hàng đủ',
                'description' => 'Giá trị đơn hàng để được miễn phí ship'
            ],
            [
                'key' => 'shipping_enabled_methods',
                'value' => 'Giao hàng tiết kiệm, Giao hàng nhanh, J&T Express',
                'type' => 'textarea',
                'group' => 'shipping',
                'label' => 'Đơn vị vận chuyển',
                'description' => 'Danh sách đơn vị vận chuyển hỗ trợ'
            ],

            // Email Notifications
            [
                'key' => 'email_order_notification',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'email',
                'label' => 'Email khi có đơn hàng mới',
                'description' => 'Gửi email thông báo đơn hàng mới'
            ],
            [
                'key' => 'email_order_status_update',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'email',
                'label' => 'Email cập nhật trạng thái đơn hàng',
                'description' => 'Gửi email khi trạng thái đơn hàng thay đổi'
            ],
            [
                'key' => 'email_newsletter',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'email',
                'label' => 'Email khuyến mãi',
                'description' => 'Gửi email thông báo khuyến mãi'
            ],

            // System
            [
                'key' => 'timezone',
                'value' => 'Asia/Ho_Chi_Minh',
                'type' => 'text',
                'group' => 'system',
                'label' => 'Múi giờ',
                'description' => 'Múi giờ hệ thống'
            ],
            [
                'key' => 'currency',
                'value' => 'VND',
                'type' => 'text',
                'group' => 'system',
                'label' => 'Đơn vị tiền tệ',
                'description' => 'Đơn vị tiền tệ sử dụng'
            ],
            [
                'key' => 'language',
                'value' => 'vi',
                'type' => 'text',
                'group' => 'system',
                'label' => 'Ngôn ngữ',
                'description' => 'Ngôn ngữ mặc định'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }

        echo "✅ Đã seed " . count($settings) . " settings!\n";
    }
}
