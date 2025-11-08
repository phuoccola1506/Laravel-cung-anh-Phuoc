<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',   // liên kết đến sản phẩm
        'sku',          // mã SKU riêng cho mỗi variant
        'price',        // giá của variant
        'stock',        // số lượng tồn
        'discount',     // giảm giá riêng cho variant
        'image',        // ảnh riêng cho variant (nếu có)
        'attributes',   // JSON lưu các thông số khác: color, storage, ram, pin, screen_size, ...
        'active',       // trạng thái
    ];

    protected $casts = [
        'attributes' => 'array', // Tự động decode JSON attributes
        'active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Lấy một thuộc tính từ JSON attributes
     * Ví dụ: $variant->attr('color'), $variant->attr('storage')
     */
    public function attr($key, $default = null)
    {
        return $this->attributes['attributes'][$key] ?? $default;
    }

    /**
     * Magic method để truy cập thuộc tính động
     * Cho phép: $variant->color, $variant->storage, $variant->ram...
     */
    public function __get($key)
    {
        // Kiểm tra xem có phải thuộc tính của model không
        if (array_key_exists($key, $this->attributes)) {
            return parent::__get($key);
        }

        // Nếu không, thử tìm trong JSON attributes
        $attrs = $this->attributes['attributes'] ?? [];
        if (is_array($attrs) && isset($attrs[$key])) {
            return $attrs[$key];
        }

        return parent::__get($key);
    }
}
