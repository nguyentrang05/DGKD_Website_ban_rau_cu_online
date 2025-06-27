<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

   
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    /**
     * Mỗi cart item thuộc về 1 giỏ hàng.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Mỗi cart item tương ứng với 1 sản phẩm.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Tổng giá của 1 cart item.
     */
    public function getSubtotalAttribute()
    {
        return $this->product->price * $this->quantity;
    }
}
