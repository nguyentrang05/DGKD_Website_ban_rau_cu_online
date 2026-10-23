<?php

namespace Tests\Feature\WhiteBox;

use App\Models\CartItem;
use PHPUnit\Framework\Attributes\DataProvider;

class CartValidationDefectTest extends WhiteBoxTestCase
{
    public static function invalidQuantities(): array
    {
        return [
            'D1_zero' => [0],
            'D2_negative' => [-1],
            'D3_fractional' => [1.5],
        ];
    }

    // Assumption to approve in the SRS: cart quantity is a positive integer.
    // Tests encode the DESIRED behavior and may fail before the source is fixed.
    #[DataProvider('invalidQuantities')]
    public function test_invalid_quantity_is_rejected_without_mutation(int|float $quantity): void
    {
        $user = $this->makeUser();
        $product = $this->makeProduct(10);

        $response = $this->actingAs($user)->postJson(
            route('cart.add', $product->id), ['quantity' => $quantity]
        );

        $this->assertSame(422, $response->getStatusCode(), sprintf(
            'quantity=%s; actual HTTP=%s; stock=%s; cart quantities=%s. Expected HTTP 422 and no mutation.',
            (string) $quantity,
            $response->getStatusCode(),
            (string) $product->fresh()->stock_quantity,
            json_encode(CartItem::pluck('quantity')->all())
        ));
        $response->assertJsonValidationErrors('quantity');
        $this->assertDatabaseCount('carts', 0);
        $this->assertDatabaseCount('cart_items', 0);
        $this->assertSame(10, (int) $product->fresh()->stock_quantity);
    }
}
