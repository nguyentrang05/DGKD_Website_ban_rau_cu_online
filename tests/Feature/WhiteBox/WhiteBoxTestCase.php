<?php

namespace Tests\Feature\WhiteBox;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use LogicException;
use Tests\TestCase;

abstract class WhiteBoxTestCase extends TestCase
{
    use RefreshDatabase;

    protected const PASSWORD = 'WhiteBox@123';

    // RefreshDatabase invokes this hook BEFORE any migrate:fresh command.
    protected function beforeRefreshingDatabase(): void
    {
        if (! $this->app->environment('testing')
            || config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:'
            || ! empty(config('database.connections.sqlite.url'))) {
            throw new LogicException(
                'WhiteBox tests require testing + SQLite :memory:. '
                .'Run php artisan config:clear, then use phpunit.whitebox.xml.'
            );
        }
    }

    protected function makeUser(string $role = 'user', string $email = 'wb@example.com'): User
    {
        return User::create([
            'name' => 'WhiteBox Student',
            'email' => $email,
            'password' => Hash::make(self::PASSWORD),
            'role' => $role,
        ]);
    }

    protected function makeProduct(int $stock = 10): Product
    {
        $category = Category::create(['name' => 'Rau cu WB', 'slug' => 'rau-cu-wb']);

        return Product::create([
            'name' => 'Ca rot WB',
            'slug' => 'ca-rot-wb',
            'description' => 'Du lieu kiem thu tu tao, khong dung seeder.',
            'price' => 15000,
            'stock_quantity' => $stock,
            'unit' => 'kg',
            'category_id' => $category->id,
        ]);
    }
}
