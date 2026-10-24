<?php

namespace Tests\Feature\BlackBox;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Nguyên tắc: chỉ thao tác qua giao diện công khai (URL, form, JSON) và quan sát kết quả
 * (trang trả về, thông báo, dữ liệu sau thao tác). không gọi hàm nội bộ.
 */
abstract class BlackBoxTestCase extends TestCase
{
    use RefreshDatabase;

    protected const PASSWORD = 'Matkhau@123';

    private int $sequence = 0;

    protected function createUser(string $role = 'user', ?string $email = null): User
    {
        $this->sequence++;

        return User::create([
            'name'     => 'Nguoi dung '.$this->sequence,
            'email'    => $email ?? 'user'.$this->sequence.'@example.com',
            'password' => Hash::make(self::PASSWORD),
            'role'     => $role,
        ]);
    }

    protected function createProduct(int $stock = 10, int $price = 15000, ?string $name = null): Product
    {
        $this->sequence++;
        $category = Category::firstOrCreate(['slug' => 'rau-cu'], ['name' => 'Rau củ']);

        return Product::create([
            'name'           => $name ?? 'San pham '.$this->sequence,
            'slug'           => 'san-pham-'.$this->sequence,
            'description'    => 'Du lieu kiem thu hop den',
            'price'          => $price,
            'stock_quantity' => $stock,
            'unit'           => 'kg',
            'image_url'      => 'products/test.jpg',
            'category_id'    => $category->id,
        ]);
    }

    protected function validRegistration(array $overrides = []): array
    {
        return array_replace([
            'name'                  => 'Nguyen Van A',
            'email'                 => 'nguyenvana@example.com',
            'phone'                 => '0901234567',
            'address'               => 'Ha Noi',
            'password'              => self::PASSWORD,
            'password_confirmation' => self::PASSWORD,
            'terms'                 => 'on',
        ], $overrides);
    }

    protected function stockOf(Product $product): int
    {
        return (int) $product->fresh()->stock_quantity;
    }
}