<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'amount',
        'percentage',
        'start_date',
        'end_date',
        'usage_limit',
        'used_count',
        'min_purchase',
        'max_discount',
        'active',
        'description'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'active' => 'boolean',
        'value' => 'decimal:2',
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'discount_user')
            ->withTimestamps();
    }

    public function isValid()
    {
        if (!$this->active) {
            return false;
        }

        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
