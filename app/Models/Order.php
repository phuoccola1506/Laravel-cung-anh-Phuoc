<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'discount_id',
        'order_code',
        'subtotal',
        'shipping_fee',
        'discount',
        'status',
        'payment_status',
        'payment_method',
        'shipping_address',
        'notes'
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'shipping_fee' => 'integer',
        'discount' => 'integer',
        'total' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Accessors for customer information
    public function getCustomerNameAttribute()
    {
        // Parse customer name from shipping_address
        if (preg_match('/Người nhận:\s*([^\n]+)/', $this->shipping_address, $matches)) {
            return trim($matches[1]);
        }
        return $this->user ? $this->user->name : 'Khách hàng';
    }

    public function getCustomerEmailAttribute()
    {
        // Parse email from shipping_address
        if (preg_match('/Email:\s*([^\n]+)/', $this->shipping_address, $matches)) {
            return trim($matches[1]);
        }
        return $this->user ? $this->user->email : '';
    }

    public function getCustomerPhoneAttribute()
    {
        // Parse phone from shipping_address
        if (preg_match('/SĐT:\s*([^\n]+)/', $this->shipping_address, $matches)) {
            return trim($matches[1]);
        }
        return $this->user ? $this->user->phone : '';
    }

    public function getDiscountAmountAttribute()
    {
        return $this->discount ?? 0;
    }
}
