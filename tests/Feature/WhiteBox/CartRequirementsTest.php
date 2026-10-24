<?php

namespace Tests\Feature\WhiteBox;

use App\Models\CartItem;

class CartRequirementsTest extends ExtendedWhiteBoxTestCase
{
    public function test_CR01_cart_has_an_editable_quantity_control_as_required_in_report(): void
    {
        $user = $this->makeUser();
        $product = $this->newProduct(['stock_quantity' => 8]);
        $this->putItem($this->cartFor($user), $product, 2);
        $xpath = $this->htmlXPath($this->actingAs($user)->get(route('cart')));
        $editable = $xpath->query('//input['.$this->classSelector('quantity-input').' and not(@readonly) and not(@disabled)]');
        $this->assertGreaterThan(0, $editable->length,
            'Report requires quantity increase/decrease. Current cart input is readonly and no update route exists.');
    }

    public function test_CR02_cart_total_price_accessor_sums_multiple_items(): void
    {
        $user = $this->makeUser();
        $cart = $this->cartFor($user);
        $this->putItem($cart, $this->newProduct(['stock_quantity' => 8]), 2);
        $this->putItem($cart, $this->newProduct(['stock_quantity' => 9, 'price' => 20000]), 1);
        // No catch: an application exception must remain visible as an Error.
        $this->assertEquals(50000, $cart->fresh()->total_price);
    }

    public function test_CR03_add_uses_active_cart_instead_of_old_inactive_cart(): void
    {
        $user = $this->makeUser();
        $inactive = $this->cartFor($user, false);
        $active = $this->cartFor($user, true);
        $product = $this->newProduct(['stock_quantity' => 9]);
        $current = $this->putItem($active, $product, 1);
        $this->actingAs($user)->post(route('cart.add', $product->id), ['quantity' => 2])->assertRedirect();
        $this->assertSame(3, (int) $current->fresh()->quantity,
            'New quantity should be added to the active cart. Check firstOrCreate(user_id) in add().');
        $this->assertSame(0, CartItem::where('cart_id', $inactive->id)->count());
    }
}
