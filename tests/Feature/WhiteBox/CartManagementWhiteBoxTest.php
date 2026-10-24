<?php

namespace Tests\Feature\WhiteBox;

use App\Models\CartItem;

class CartManagementWhiteBoxTest extends ExtendedWhiteBoxTestCase
{
    public function test_CM01_delete_last_item_restores_stock_and_reports_empty(): void
    {
        $user = $this->makeUser();
        $product = $this->newProduct(['stock_quantity' => 7]);
        $item = $this->putItem($this->cartFor($user), $product, 3);
        $this->actingAs($user)->deleteJson(route('cart.remove', $item->id))
            ->assertOk()->assertJson(['success' => true, 'cartEmpty' => true]);
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
        $this->assertSame(10, (int) $product->fresh()->stock_quantity);
    }

    public function test_CM02_delete_one_of_two_items_preserves_other_item(): void
    {
        $user = $this->makeUser();
        $cart = $this->cartFor($user);
        $first = $this->newProduct(['stock_quantity' => 8]);
        $second = $this->newProduct(['stock_quantity' => 9, 'price' => 20000]);
        $removed = $this->putItem($cart, $first, 2);
        $kept = $this->putItem($cart, $second, 1);
        $this->actingAs($user)->deleteJson(route('cart.remove', $removed->id))
            ->assertOk()->assertJson(['success' => true, 'cartEmpty' => false]);
        $this->assertDatabaseCount('cart_items', 1);
        $this->assertDatabaseHas('cart_items', ['id' => $kept->id, 'quantity' => 1]);
        $this->assertSame(10, (int) $first->fresh()->stock_quantity);
        $this->assertSame(9, (int) $second->fresh()->stock_quantity);
    }

    public function test_CM03_missing_item_returns_failure_without_mutating_cart(): void
    {
        $user = $this->makeUser();
        $product = $this->newProduct(['stock_quantity' => 8]);
        $item = $this->putItem($this->cartFor($user), $product, 2);
        $this->actingAs($user)->deleteJson(route('cart.remove', 999999))
            ->assertOk()->assertJson(['success' => false]);
        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 2]);
        $this->assertSame(8, (int) $product->fresh()->stock_quantity);
    }

    public function test_CM04_cannot_delete_another_users_item(): void
    {
        $owner = $this->makeUser('user', 'owner@example.com');
        $other = $this->makeUser('user', 'other@example.com');
        $product = $this->newProduct(['stock_quantity' => 8]);
        $item = $this->putItem($this->cartFor($owner), $product, 2);
        $this->actingAs($other)->deleteJson(route('cart.remove', $item->id))
            ->assertOk()->assertJson(['success' => false]);
        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 2]);
        $this->assertSame(8, (int) $product->fresh()->stock_quantity);
    }

    public function test_CM05_guest_cannot_delete_cart_item(): void
    {
        $user = $this->makeUser();
        $product = $this->newProduct(['stock_quantity' => 8]);
        $item = $this->putItem($this->cartFor($user), $product, 2);
        $this->delete(route('cart.remove', $item->id))->assertRedirect(route('login'));
        $this->assertDatabaseHas('cart_items', ['id' => $item->id]);
        $this->assertSame(8, (int) $product->fresh()->stock_quantity);
    }

    public function test_CM06_out_of_stock_product_cannot_be_added(): void
    {
        $user = $this->makeUser();
        $product = $this->newProduct(['stock_quantity' => 0]);
        $this->actingAs($user)->post(route('cart.add', $product->id), ['quantity' => 1])
            ->assertRedirect()->assertSessionHas('error');
        $this->assertDatabaseCount('cart_items', 0);
        $this->assertSame(0, (int) $product->fresh()->stock_quantity);
    }

    public function test_CM07_no_cart_shows_empty_message_and_no_checkout_form(): void
    {
        $response = $this->actingAs($this->makeUser())->get(route('cart'));
        $response->assertOk()->assertSee('Giỏ hàng của bạn đang trống');
        $xpath = $this->htmlXPath($response);
        $this->assertSame(0, $xpath->query('//form[@id="checkoutForm"]')->length);
    }

    public function test_CM08_existing_empty_cart_shows_empty_message(): void
    {
        $user = $this->makeUser();
        $this->cartFor($user);
        $this->actingAs($user)->get(route('cart'))->assertOk()->assertSee('Giỏ hàng của bạn đang trống');
    }

    public function test_CM09_cart_renders_item_details_quantities_subtotals_and_total(): void
    {
        $user = $this->makeUser();
        $cart = $this->cartFor($user);
        $first = $this->newProduct(['name' => 'Ca rot WB', 'stock_quantity' => 8]);
        $second = $this->newProduct(['name' => 'Cai xanh WB', 'stock_quantity' => 9, 'price' => 20000]);
        $items = [$this->putItem($cart, $first, 2), $this->putItem($cart, $second, 1)];
        $response = $this->actingAs($user)->get(route('cart'));
        $this->assertCartTotal($response, 50000);
        $xpath = $this->htmlXPath($response);
        $this->assertSame(2, $xpath->query('//*['.$this->classSelector('cart-item').']')->length);
        foreach ($items as $item) {
            $row = '//*[@data-item-id="'.$item->id.'"]';
            $product = $item->product;
            $this->assertStringContainsString($product->name, $this->textAt($xpath, $row));
            $this->assertStringContainsString('kg', $this->textAt($xpath, $row));
            $this->assertStringContainsString(number_format($product->price, 0, ',', '.').'đ', $this->textAt($xpath, $row));
            $this->assertSame(1, $xpath->query($row.'//input[@value="'.$item->quantity.'"]')->length);
            $this->assertSame(1, $xpath->query($row.'//img[@alt="'.$product->name.'"]')->length);
            $image = $xpath->query($row.'//img')->item(0);
            $this->assertStringEndsWith('/storage/whitebox/product.jpg', $image->getAttribute('src'));
            $this->assertSame(number_format($product->price * $item->quantity, 0, ',', '.').'đ',
                $this->textAt($xpath, $row.'//*['.$this->classSelector('item-total').']'));
        }
        // Existing UI counts distinct product rows, not the sum of item quantities.
        $response->assertSee('2 sản phẩm');
        $this->assertSame(1, $xpath->query('//form[@id="checkoutForm"]//button[@type="submit" and not(@disabled)]')->length);
    }

    public function test_CM10_total_is_recalculated_after_adding_to_existing_item(): void
    {
        $user = $this->makeUser();
        $cart = $this->cartFor($user);
        $first = $this->newProduct(['stock_quantity' => 8]);
        $second = $this->newProduct(['stock_quantity' => 9, 'price' => 20000]);
        $item = $this->putItem($cart, $first, 2);
        $this->putItem($cart, $second, 1);
        $this->actingAs($user)->post(route('cart.add', $first->id), ['quantity' => 2])
            ->assertRedirect()->assertSessionHas('success');
        $this->assertSame(4, (int) $item->fresh()->quantity);
        $this->assertSame(6, (int) $first->fresh()->stock_quantity);
        $this->assertCartTotal($this->get(route('cart')), 80000);
    }

    public function test_CM11_total_is_recalculated_after_deleting_one_item(): void
    {
        $user = $this->makeUser();
        $cart = $this->cartFor($user);
        $first = $this->newProduct(['stock_quantity' => 8]);
        $second = $this->newProduct(['stock_quantity' => 9, 'price' => 20000]);
        $removed = $this->putItem($cart, $first, 2);
        $kept = $this->putItem($cart, $second, 1);
        $this->actingAs($user)->deleteJson(route('cart.remove', $removed->id))->assertJson(['success' => true]);
        $response = $this->get(route('cart'));
        $this->assertCartTotal($response, 20000);
        $xpath = $this->htmlXPath($response);
        $this->assertSame(0, $xpath->query('//*[@data-item-id="'.$removed->id.'"]')->length);
        $this->assertSame(1, $xpath->query('//*[@data-item-id="'.$kept->id.'"]')->length);
    }

    public function test_CM12_last_deletion_returns_empty_page_without_checkout(): void
    {
        $user = $this->makeUser();
        $product = $this->newProduct(['stock_quantity' => 8]);
        $item = $this->putItem($this->cartFor($user), $product, 2);
        $this->actingAs($user)->deleteJson(route('cart.remove', $item->id))->assertJson(['cartEmpty' => true]);
        $response = $this->get(route('cart'));
        $response->assertOk()->assertSee('Giỏ hàng của bạn đang trống');
        $this->assertSame(0, $this->htmlXPath($response)->query('//form[@id="checkoutForm"]')->length);
    }

    public function test_CM13_cart_page_only_displays_active_cart(): void
    {
        $user = $this->makeUser();
        $inactive = $this->cartFor($user, false);
        $active = $this->cartFor($user, true);
        $old = $this->putItem($inactive, $this->newProduct(['stock_quantity' => 8]), 2);
        $current = $this->putItem($active, $this->newProduct(['stock_quantity' => 9]), 1);
        $response = $this->actingAs($user)->get(route('cart'));
        $this->assertCartTotal($response, 15000);
        $xpath = $this->htmlXPath($response);
        $this->assertSame(0, $xpath->query('//*[@data-item-id="'.$old->id.'"]')->length);
        $this->assertSame(1, $xpath->query('//*[@data-item-id="'.$current->id.'"]')->length);
    }
}
