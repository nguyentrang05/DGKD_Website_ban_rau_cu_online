<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use  HasFactory, Notifiable;

   
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'phone',
        'role',
        'date_of_birth',
        'gender',
    ];
    //Ẩn thông tin các cột (Bảo mật)
    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',//Tự động mã hóa mật khẩu
    ];

    //Một người dùng có nhiều giỏ hàng
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    //Lấy ra giỏ hàng hiện tại của người dùng
    public function activeCart()
    {
        return $this->hasOne(Cart::class)->where('is_active', true);
    }
    //Một người dùng có nhiều đơn hàng
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    //Phân quyền người dùng
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }
}
