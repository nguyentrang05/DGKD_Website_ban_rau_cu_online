<?php

namespace Tests\Feature\BlackBox;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;

class OrderBlackBoxTest extends BlackBoxTestCase
{
    private function userWithCart(int $quantity = 2, int $price = 15000): array
    {
        $user = $this->createUser();
        $product = $this->createProduct(10, $price);
        $this->actingAs($user);
        $this->post(route('cart.add', $product->id), ['quantity' => $quantity]);

        return [$user, $product];
    }

    private function createOrderFor(User $user, string $status, string $paymentStatus): Order
    {
        return Order::create([
            'user_id'        => $user->id,
            'total_price'    => 15000,
            'status'         => $status,
            'payment_status' => $paymentStatus,
            'payment_method' => 'qrcode',
            'address'        => 'Ha Noi',
        ]);
    }

    // GH12: thanh toán COD
    public function test_GH12_checkout_cod_creates_pending_unpaid_order_and_empties_cart(): void
    {
        [$user, $product] = $this->userWithCart(2, 15000);

        $this->post(route('orders.checkout'), ['payment_method' => 'COD'])
            ->assertRedirect(route('cart'))
            ->assertSessionHas('success');

        $order = Order::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('pending', $order->status);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame('COD', $order->payment_method);
        $this->assertEquals(30000, $order->total_price);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 2]);

        $this->get(route('cart'))->assertSee('Giỏ hàng của bạn đang trống');
    }

    // GH12b: đơn hàng xuất hiện trong lịch sử đơn của chính người đặt
    public function test_GH12b_order_appears_in_order_history(): void
    {
        [$user] = $this->userWithCart();
        $this->post(route('orders.checkout'), ['payment_method' => 'COD']);
        $order = Order::where('user_id', $user->id)->firstOrFail();

        $this->get(route('orders.index'))->assertOk();
        $this->get(route('orders.show', $order->id))->assertOk();
    }

    // GH13: thanh toán khi giỏ trống
    public function test_GH13_checkout_with_empty_cart_is_rejected(): void
    {
        $this->actingAs($this->createUser());

        $this->from(route('cart'))->post(route('orders.checkout'), ['payment_method' => 'COD'])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('orders', 0);
    }

    // GH13b: phương thức thanh toán không hợp lệ
    public function test_GH13b_invalid_payment_method_is_rejected(): void
    {
        $this->userWithCart();

        $this->from(route('cart'))->post(route('orders.checkout'), ['payment_method' => 'bitcoin'])
            ->assertSessionHasErrors('payment_method');

        $this->assertDatabaseCount('orders', 0);
    }

    // GH13c: ngày giao hàng ở quá khứ
    public function test_GH13c_delivery_date_in_the_past_is_rejected(): void
    {
        $this->userWithCart();

        $this->from(route('cart'))->post(route('orders.checkout'), [
            'payment_method' => 'COD',
            'delivery_date'  => now()->subDays(3)->format('Y-m-d'),
        ])->assertSessionHasErrors('delivery_date');

        $this->assertDatabaseCount('orders', 0);
    }

    // GH14: thanh toán QR rồi đóng cửa sổ QR (hủy đơn) — hàng trả về giỏ
    public function test_GH14_qr_checkout_then_cancel_returns_items_to_cart(): void
    {
        [$user, $product] = $this->userWithCart(2);

        $response = $this->postJson(route('orders.checkout'), ['payment_method' => 'qrcode'])
            ->assertOk()
            ->assertJson(['success' => true]);
        $orderId = $response->json('order_id');

        $this->postJson(route('orders.cancel', $orderId))->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', ['id' => $orderId, 'status' => 'cancelled']);
        $this->get(route('cart'))->assertSee($product->name);
    }

    // GH14b: hủy hai lần không gây lỗi
    public function test_GH14b_cancelling_twice_is_harmless(): void
    {
        $this->userWithCart();
        $orderId = $this->postJson(route('orders.checkout'), ['payment_method' => 'qrcode'])->json('order_id');

        $this->postJson(route('orders.cancel', $orderId))->assertJson(['success' => true]);
        $this->postJson(route('orders.cancel', $orderId))->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', ['id' => $orderId, 'status' => 'cancelled']);
    }

    // GH15: không hủy được đơn đã thanh toán hoặc đã hoàn thành
    public static function nonCancellableStates(): array
    {
        return [
            'da_hoan_thanh_va_da_tra_tien' => ['completed', 'paid'],
            'dang_xu_ly'                   => ['processing', 'unpaid'],
            'cho_xu_ly_nhung_da_tra_tien'  => ['pending', 'paid'],
        ];
    }

    #[DataProvider('nonCancellableStates')]
    public function test_GH15_order_in_final_or_paid_state_cannot_be_cancelled(string $status, string $payment): void
    {
        $user = $this->createUser();
        $order = $this->createOrderFor($user, $status, $payment);

        $this->actingAs($user)->postJson(route('orders.cancel', $order->id))->assertStatus(422);

        $this->assertSame($status, $order->fresh()->status);
    }

    // GH20: không xem được đơn hàng của người khác
    public function test_GH20_user_cannot_view_another_users_order(): void
    {
        $owner = $this->createUser();
        $order = $this->createOrderFor($owner, 'pending', 'unpaid');

        $this->actingAs($this->createUser())->get(route('orders.show', $order->id))->assertNotFound();
    }

    // GH21: không hủy được đơn của người khác
    public function test_GH21_user_cannot_cancel_another_users_order(): void
    {
        $owner = $this->createUser();
        $order = $this->createOrderFor($owner, 'pending', 'unpaid');

        $this->actingAs($this->createUser())->postJson(route('orders.cancel', $order->id))->assertNotFound();

        $this->assertSame('pending', $order->fresh()->status);
    }

    // GH22: giá trong đơn hàng giữ nguyên dù giá sản phẩm đổi sau đó
    public function test_GH22_order_keeps_price_at_purchase_time(): void
    {
        [$user, $product] = $this->userWithCart(2, 15000);
        $this->post(route('orders.checkout'), ['payment_method' => 'COD']);
        $order = Order::where('user_id', $user->id)->firstOrFail();

        Product::whereKey($product->id)->update(['price' => 99000]);

        $this->assertEquals(30000, $order->fresh()->total_price);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'price' => 15000]);
    }

    // AD01: admin xác nhận thanh toán
    public function test_AD01_admin_can_confirm_payment(): void
    {
        $order = $this->createOrderFor($this->createUser(), 'pending', 'unpaid');

        $this->actingAs($this->createUser('admin'))
            ->post(route('admin.orders.confirmPayment', $order->id))
            ->assertRedirect(route('admin.orders.show', $order->id));

        $fresh = $order->fresh();
        $this->assertSame('paid', $fresh->payment_status);
        $this->assertSame('completed', $fresh->status);
    }

    // AD02: user thường không tự xác nhận thanh toán được
    public function test_AD02_normal_user_cannot_confirm_payment(): void
    {
        $user = $this->createUser();
        $order = $this->createOrderFor($user, 'pending', 'unpaid');

        $this->actingAs($user)->post(route('admin.orders.confirmPayment', $order->id))->assertForbidden();

        $this->assertSame('unpaid', $order->fresh()->payment_status);
    }
}