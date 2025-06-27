<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Một mục đơn hàng thuộc về 1 đơn hàng.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Mỗi mục đơn hàng thuộc về 1 sản phẩm.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Tính tổng giá từng mục đơn hàng.
     */
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}
