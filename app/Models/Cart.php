<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    /**
     * Chỉ cho phép gán các cột trong $fillable .
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'is_active',
    ];

    /**
     * Tự động ép kiểu is_active khi lấy dữ liệu từ DB.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];
    //Thiết lập mối quan hệ 
    /**
     * Mỗi người dùng có 1 giỏ hàng.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Một giỏ hàng có nhiều món hàng.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Tính tổng giá tiền.
     */
    public function getTotalPriceAttribute()
    {
        return $this->cartItems()->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
    }
}
