<?php

namespace Tests\Feature\BlackBox;

use App\Models\CartItem;
use App\Models\Product;
use PHPUnit\Framework\Attributes\DataProvider;

class CartBlackBoxTest extends BlackBoxTestCase
{
    private const EMPTY_MESSAGE = 'Giỏ hàng của bạn đang trống';

    private function addToCart(Product $product, $quantity)
    {
        return $this->from(route('products.show', $product->id))
            ->post(route('cart.add', $product->id), ['quantity' => $quantity]);
    }

    private function itemIdOf(Product $product): int
    {
        return CartItem::where('product_id', $product->id)->firstOrFail()->id;
    }

    // GH01: thêm 1 sản phẩm
    public function test_GH01_add_one_product(): void
    {
        $this->actingAs($this->createUser());
        $product = $this->createProduct(10);

        $this->addToCart($product, 1)->assertSessionHas('success', 'Đã thêm sản phẩm vào giỏ hàng');

        $this->get(route('cart'))->assertOk()->assertSee($product->name);
        $this->assertSame(9, $this->stockOf($product));
    }

    // GH01b: không gửi số lượng thì mặc định là 1
    public function test_GH01b_default_quantity_is_one(): void
    {
        $this->actingAs($this->createUser());
        $product = $this->createProduct(10);

        $this->post(route('cart.add', $product->id))->assertSessionHas('success');

        $this->assertSame(9, $this->stockOf($product));
    }

    // GH02: thêm đúng bằng tồn kho (biên)
    public function test_GH02_add_exactly_the_stock(): void
    {
        $this->actingAs($this->createUser());
        $product = $this->createProduct(10);

        $this->addToCart($product, 10)->assertSessionHas('success');

        $this->assertSame(0, $this->stockOf($product));
    }

    // GH03: thêm vượt tồn kho 1 đơn vị (biên + 1)
    public function test_GH03_add_more_than_stock_is_rejected(): void
    {
        $this->actingAs($this->createUser());
        $product = $this->createProduct(10);

        $this->addToCart($product, 11)->assertSessionHas('error', 'Sản phẩm không đủ số lượng tồn kho');

        $this->assertSame(10, $this->stockOf($product));
        $this->get(route('cart'))->assertSee(self::EMPTY_MESSAGE);
    }

    // GH04-GH06: số lượng không hợp lệ
    public static function invalidQuantities(): array
    {
        return [
            'GH04_bang_0'      => [0],
            'GH05_so_am'       => [-1],
            'GH06_so_thap_phan' => [1.5],
            'GH06b_chu'        => ['abc'],
        ];
    }

    #[DataProvider('invalidQuantities')]
    public function test_GH04_invalid_quantity_is_rejected(int|float|string $quantity): void
    {
        $this->actingAs($this->createUser());
        $product = $this->createProduct(10);

        $this->addToCart($product, $quantity)->assertSessionHasErrors('quantity');

        $this->assertSame(10, $this->stockOf($product));
        $this->assertDatabaseCount('cart_items', 0);
    }

    // GH07: thêm cùng một sản phẩm hai lần thì gộp thành một dòng
    public function test_GH07_adding_same_product_twice_merges_into_one_line(): void
    {
        $this->actingAs($this->createUser());
        $product = $this->createProduct(10);

        $this->addToCart($product, 2);
        $this->addToCart($product, 3);

        $this->assertDatabaseCount('cart_items', 1);
        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 5]);
        $this->assertSame(5, $this->stockOf($product));
    }

    // GH08: sản phẩm hết hàng
    public function test_GH08_out_of_stock_product_cannot_be_added(): void
    {
        $this->actingAs($this->createUser());
        $product = $this->createProduct(0);

        $this->addToCart($product, 1)->assertSessionHas('error');

        $this->assertDatabaseCount('cart_items', 0);
    }

    // GH08b: sản phẩm không tồn tại
    public function test_GH08b_missing_product_returns_404(): void
    {
        $this->actingAs($this->createUser());

        $this->post(route('cart.add', 999999), ['quantity' => 1])->assertNotFound();
    }

    // GH09: xóa 1 trong 2 sản phẩm
    public function test_GH09_remove_one_of_two_products(): void
    {
        $this->actingAs($this->createUser());
        $first = $this->createProduct(10, 15000, 'Ca rot test');
        $second = $this->createProduct(10, 20000, 'Cai xanh test');
        $this->addToCart($first, 2);
        $this->addToCart($second, 1);
        $removedId = $this->itemIdOf($first);
        $keptId = $this->itemIdOf($second);

        $this->deleteJson(route('cart.remove', $removedId))
            ->assertOk()
            ->assertJson(['success' => true, 'cartEmpty' => false]);

        $this->assertSame(10, $this->stockOf($first));
        $this->assertSame(9, $this->stockOf($second));

        // Trang giỏ còn dòng của sản phẩm thứ hai, không còn dòng của sản phẩm thứ nhất.
        // (Không kiểm tra theo tên vì khung "Gợi ý" có thể hiển thị tên sản phẩm ngẫu nhiên.)
        $this->get(route('cart'))
            ->assertSee('data-item-id="'.$keptId.'"', false)
            ->assertDontSee('data-item-id="'.$removedId.'"', false);
    }

    // GH10: xóa sản phẩm cuối cùng
    public function test_GH10_remove_last_product_shows_empty_cart(): void
    {
        $this->actingAs($this->createUser());
        $product = $this->createProduct(10);
        $this->addToCart($product, 2);

        $this->deleteJson(route('cart.remove', $this->itemIdOf($product)))
            ->assertOk()
            ->assertJson(['success' => true, 'cartEmpty' => true]);

        $this->assertSame(10, $this->stockOf($product));
        $this->get(route('cart'))->assertSee(self::EMPTY_MESSAGE);
    }

    // GH10b: xóa dòng không tồn tại
    public function test_GH10b_remove_missing_line_fails_without_changes(): void
    {
        $this->actingAs($this->createUser());
        $product = $this->createProduct(10);
        $this->addToCart($product, 2);

        $this->deleteJson(route('cart.remove', 999999))->assertJson(['success' => false]);

        $this->assertDatabaseCount('cart_items', 1);
        $this->assertSame(8, $this->stockOf($product));
    }

    // GH11: sửa số lượng ngay trong giỏ — tăng
    public function test_GH11_increase_quantity_in_cart(): void
    {
        $this->actingAs($this->createUser());
        $product = $this->createProduct(10);
        $this->addToCart($product, 2);

        $this->patchJson(route('cart.update', $this->itemIdOf($product)), ['quantity' => 5])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 5]);
        $this->assertSame(5, $this->stockOf($product));
    }

    // GH11b: sửa số lượng — giảm
    public function test_GH11b_decrease_quantity_in_cart(): void
    {
        $this->actingAs($this->createUser());
        $product = $this->createProduct(10);
        $this->addToCart($product, 5);

        $this->patchJson(route('cart.update', $this->itemIdOf($product)), ['quantity' => 2])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 2]);
        $this->assertSame(8, $this->stockOf($product));
    }

    // GH11c: sửa số lượng vượt tồn kho
    public function test_GH11c_update_beyond_stock_is_rejected(): void
    {
        $this->actingAs($this->createUser());
        $product = $this->createProduct(10);
        $this->addToCart($product, 2); // tồn kho còn 8

        $this->patchJson(route('cart.update', $this->itemIdOf($product)), ['quantity' => 11])
            ->assertJson(['success' => false]);

        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 2]);
        $this->assertSame(8, $this->stockOf($product));
    }

    // GH11d: sửa số lượng không hợp lệ
    public static function invalidUpdateQuantities(): array
    {
        return ['bang_0' => [0], 'so_am' => [-3], 'thap_phan' => [1.5], 'chu' => ['abc']];
    }

    #[DataProvider('invalidUpdateQuantities')]
    public function test_GH11d_update_with_invalid_quantity_is_rejected(int|float|string $quantity): void
    {
        $this->actingAs($this->createUser());
        $product = $this->createProduct(10);
        $this->addToCart($product, 2);

        $this->patchJson(route('cart.update', $this->itemIdOf($product)), ['quantity' => $quantity])
            ->assertUnprocessable();

        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 2]);
    }

    // GH16: không thao tác được trên giỏ hàng của người khác
    public function test_GH16_user_cannot_change_or_remove_another_users_item(): void
    {
        $owner = $this->createUser();
        $other = $this->createUser();
        $product = $this->createProduct(10);

        $this->actingAs($owner);
        $this->addToCart($product, 2);
        $itemId = $this->itemIdOf($product);

        $this->actingAs($other);
        $this->deleteJson(route('cart.remove', $itemId))->assertJson(['success' => false]);
        $this->patchJson(route('cart.update', $itemId), ['quantity' => 9])->assertJson(['success' => false]);

        $this->assertDatabaseHas('cart_items', ['id' => $itemId, 'quantity' => 2]);
        $this->assertSame(8, $this->stockOf($product));
    }

    // GH17: khách chưa đăng nhập không dùng được giỏ hàng
    public function test_GH17_guest_cannot_use_cart(): void
    {
        $product = $this->createProduct(10);

        $this->get(route('cart'))->assertRedirect(route('login'));
        $this->post(route('cart.add', $product->id), ['quantity' => 1])->assertRedirect(route('login'));

        $this->assertSame(10, $this->stockOf($product));
    }

    // GH18: giỏ hàng trống khi chưa thêm gì
    public function test_GH18_new_user_sees_empty_cart(): void
    {
        $this->actingAs($this->createUser())->get(route('cart'))->assertOk()->assertSee(self::EMPTY_MESSAGE);
    }

    // GH19: mỗi người dùng chỉ thấy giỏ hàng của mình
    public function test_GH19_each_user_sees_only_their_own_cart(): void
    {
        $product = $this->createProduct(10);

        $this->actingAs($this->createUser());
        $this->addToCart($product, 1);
        $itemId = $this->itemIdOf($product);

        $this->actingAs($this->createUser())
            ->get(route('cart'))
            ->assertSee(self::EMPTY_MESSAGE)
            ->assertDontSee('data-item-id="'.$itemId.'"', false);
    }
}