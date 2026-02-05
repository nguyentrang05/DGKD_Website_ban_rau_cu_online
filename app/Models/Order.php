<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'payment_status',
        'payment_method',
        'address',
        'delivery_date',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'delivery_date' => 'date',
    ];

    /**
     * Mỗi đơn hàng thuộc về 1 người dùng.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     *Mỗi đơn hàng có nhiều dòng sp
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Backwards-compatible alias used by some admin views.
     */
    public function orderDetails()
    {
        return $this->orderItems();
    }

    //Kiểm tra trạng thái đơn hàng

    public function isPending()
    {
        return $this->status === 'pending';
    }


    public function isProcessing()
    {
        return $this->status === 'processing';
    }


    public function isCompleted()
    {
        return $this->status === 'completed';
    }


    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }
    //Tính tổng giá tại thời điểm đặt hàng
    public function getTotalAttribute()
    {
        return $this->orderItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }
}
