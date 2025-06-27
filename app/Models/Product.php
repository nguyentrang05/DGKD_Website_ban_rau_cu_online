<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

   
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock_quantity',
        'unit',
        'image_url',
        'category_id',
    ];

    
    protected $casts = [
        'price' => 'decimal:2',
    ];
    //Một sản phẩm thuộc về 1 danh mục
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    //Một sản phẩm nằm trong nhiều mục giỏ hàng
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    //Một sản phẩm nằm trong nhiều mục đơn hàng
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
