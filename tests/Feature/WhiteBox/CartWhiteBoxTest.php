<?php

namespace Tests\Feature\WhiteBox;

use App\Models\Cart;
use App\Models\CartItem;

class CartWhiteBoxTest extends WhiteBoxTestCase
{
    // A1: D1=true -> reject; no cart or stock mutation.
    public function test_A1_insufficient_stock_is_rejected(): void
    {
        $user = $this->makeUser();
        $product = $this->makeProduct(10);

        $this->actingAs($user)->from(route('products.show', $product->id))
            ->post(route('cart.add', $product->id), ['quantity' => 11])
            ->assertRedirect(route('products.show', $product->id))
            ->assertSessionHas('error', 'Sản phẩm không đủ số lượng tồn kho');

        $this->assertDatabaseCount('carts', 0);
        $this->assertDatabaseCount('cart_items', 0);
        $this->assertSame(10, (int) $product->fresh()->stock_quantity);
    }

    // A2: D1=false -> D2=false -> create item -> decrement stock.
    public function test_A2_new_item_is_created_and_stock_is_decreased(): void
    {
        $user = $this->makeUser();
        $product = $this->makeProduct(10);

        $this->actingAs($user)->from(route('products.show', $product->id))
            ->post(route('cart.add', $product->id), ['quantity' => 2])
            ->assertRedirect(route('products.show', $product->id))
            ->assertSessionHas('success', 'Đã thêm sản phẩm vào giỏ hàng');

        $cart = Cart::where('user_id', $user->id)->firstOrFail();
        $this->assertDatabaseCount('carts', 1);
        $this->assertDatabaseCount('cart_items', 1);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 2,
        ]);
        $this->assertSame(8, (int) $product->fresh()->stock_quantity);
        $item = CartItem::firstOrFail();
        $this->assertEquals(30000, $item->subtotal);
    }

    // A3: D1=false -> D2=true -> D3=false -> update existing item.
    public function test_A3_existing_item_is_incremented_without_duplicate_row(): void
    {
        $user = $this->makeUser();
        // Original stock 10; 3 units already reserved in cart; 7 remain.
        $product = $this->makeProduct(7);
        $cart = Cart::create(['user_id' => $user->id]);
        $item = CartItem::create([
            'cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 3,
        ]);

        $this->actingAs($user)->from(route('products.show', $product->id))
            ->post(route('cart.add', $product->id), ['quantity' => 2])
            ->assertRedirect(route('products.show', $product->id))
            ->assertSessionHas('success', 'Đã thêm sản phẩm vào giỏ hàng');

        $this->assertDatabaseCount('cart_items', 1);
        $this->assertSame(5, (int) $item->fresh()->quantity);
        $this->assertSame(5, (int) $product->fresh()->stock_quantity);
        $this->assertEquals(75000, $item->fresh()->subtotal);
    }

    // A4 (D1=false, D2=true, D3=true) is infeasible for ordinary integer inputs.
    // See NOI_DUNG_BAO_CAO.md. Do not manufacture inconsistent product reads.

    public function test_A5_omitted_quantity_defaults_to_one(): void
    {
        $user = $this->makeUser();
        $product = $this->makeProduct(10);

        $this->actingAs($user)->post(route('cart.add', $product->id))
            ->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 1]);
        $this->assertSame(9, (int) $product->fresh()->stock_quantity);
    }

    public function test_A6_quantity_equal_to_stock_is_accepted(): void
    {
        $user = $this->makeUser();
        $product = $this->makeProduct(10);

        $this->actingAs($user)->post(route('cart.add', $product->id), ['quantity' => 10])
            ->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 10]);
        $this->assertSame(0, (int) $product->fresh()->stock_quantity);
    }

    public function test_A7_missing_product_returns_404(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)->post(route('cart.add', 999999), ['quantity' => 1])
            ->assertNotFound();

        $this->assertDatabaseCount('carts', 0);
        $this->assertDatabaseCount('cart_items', 0);
    }

    // Access check is performed by LoginMiddleware, outside add()'s reduced CFG.
    public function test_A8_guest_is_redirected_before_cart_mutation(): void
    {
        $product = $this->makeProduct(10);

        $this->post(route('cart.add', $product->id), ['quantity' => 2])
            ->assertRedirect(route('login'))->assertSessionHasErrors('email');

        $this->assertDatabaseCount('carts', 0);
        $this->assertDatabaseCount('cart_items', 0);
        $this->assertSame(10, (int) $product->fresh()->stock_quantity);
    }
}
