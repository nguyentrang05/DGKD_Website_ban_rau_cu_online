<?php

namespace Tests\Feature\WhiteBox;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Testing\TestResponse;

abstract class ExtendedWhiteBoxTestCase extends WhiteBoxTestCase
{
    private int $productSequence = 0;

    protected function registrationData(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Nguyen WhiteBox',
            'email' => 'register.wb@example.com',
            'phone' => '0901234567',
            'address' => 'Ha Noi',
            'password' => self::PASSWORD,
            'password_confirmation' => self::PASSWORD,
            'terms' => 'on',
        ], $overrides);
    }

    protected function newProduct(array $overrides = []): Product
    {
        $this->productSequence++;
        $category = Category::firstOrCreate(
            ['slug' => 'extended-wb'], ['name' => 'Rau cu WB']
        );

        return Product::create(array_replace([
            'name' => 'San pham WB '.$this->productSequence,
            'slug' => 'san-pham-wb-'.$this->productSequence,
            'description' => 'Du lieu kiem thu tu tao',
            'price' => 15000,
            'stock_quantity' => 10,
            'unit' => 'kg',
            'image_url' => 'whitebox/product.jpg',
            'category_id' => $category->id,
        ], $overrides));
    }

    protected function cartFor(User $user, bool $active = true): Cart
    {
        return Cart::create(['user_id' => $user->id, 'is_active' => $active]);
    }

    // The product's stock fixture must already represent stock AFTER reservation.
    protected function putItem(Cart $cart, Product $product, int $quantity): CartItem
    {
        return CartItem::create([
            'cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => $quantity,
        ]);
    }

    protected function htmlXPath(TestResponse $response): DOMXPath
    {
        $response->assertOk();
        $previous = libxml_use_internal_errors(true);
        try {
            $document = new DOMDocument();
            $document->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        return new DOMXPath($document);
    }

    protected function classSelector(string $class): string
    {
        return "contains(concat(' ', normalize-space(@class), ' '), ' ".$class." ')";
    }

    protected function textAt(DOMXPath $xpath, string $expression): string
    {
        $nodes = $xpath->query($expression);
        $this->assertNotFalse($nodes);
        $this->assertGreaterThan(0, $nodes->length, 'Missing HTML element: '.$expression);
        return trim(preg_replace('/\s+/u', ' ', $nodes->item(0)->textContent));
    }

    protected function assertCartTotal(TestResponse $response, int $expected): void
    {
        $xpath = $this->htmlXPath($response);
        $this->assertSame(
            number_format($expected, 0, ',', '.').'đ',
            $this->textAt($xpath, '//*['.$this->classSelector('total-amount').']')
        );
    }
}
