<?php

namespace Tests\Feature\BlackBox;

use PHPUnit\Framework\Attributes\DataProvider;

class LoginBlackBoxTest extends BlackBoxTestCase
{
    private function attempt(string $email, string $password)
    {
        return $this->from(route('login'))->post(route('login.post'), [
            'email'    => $email,
            'password' => $password,
        ]);
    }

    // DN01: user đăng nhập đúng
    public function test_DN01_user_login_goes_to_home(): void
    {
        $user = $this->createUser('user');

        $this->attempt($user->email, self::PASSWORD)->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    // DN02: admin đăng nhập đúng
    public function test_DN02_admin_login_goes_to_dashboard(): void
    {
        $admin = $this->createUser('admin');

        $this->attempt($admin->email, self::PASSWORD)->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    // DN03: đúng email, sai mật khẩu
    public function test_DN03_wrong_password_is_rejected(): void
    {
        $user = $this->createUser();

        $this->attempt($user->email, 'SaiMatKhau@1')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Sai email hoặc mật khẩu.']);

        $this->assertGuest();
    }

    // DN04: email không tồn tại — thông báo giống DN03 (không lộ email có tồn tại hay không)
    public function test_DN04_unknown_email_gives_same_message_as_wrong_password(): void
    {
        $this->attempt('khongtontai@example.com', self::PASSWORD)
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Sai email hoặc mật khẩu.']);

        $this->assertGuest();
    }

    // DN05: để trống cả hai ô
    public function test_DN05_empty_credentials_are_rejected(): void
    {
        $this->attempt('', '')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    // DN06: email sai định dạng
    public static function invalidEmails(): array
    {
        return ['thieu_@' => ['abc.com'], 'thieu_ten_mien' => ['abc@'], 'co_khoang_trang' => ['a b@c.com']];
    }

    #[DataProvider('invalidEmails')]
    public function test_DN06_invalid_email_format_is_rejected(string $email): void
    {
        $this->attempt($email, self::PASSWORD)
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    // DN06b: mật khẩu phân biệt hoa thường
    public function test_DN06b_password_is_case_sensitive(): void
    {
        $user = $this->createUser();

        $this->attempt($user->email, strtoupper(self::PASSWORD))->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    // DN07: đã đăng nhập mà mở lại trang đăng nhập
    public function test_DN07_logged_in_user_is_redirected_away_from_login_page(): void
    {
        $this->actingAs($this->createUser('user'))->get(route('login'))->assertRedirect(route('home'));
    }

    public function test_DN07b_logged_in_admin_is_redirected_away_from_login_page(): void
    {
        $this->actingAs($this->createUser('admin'))->get(route('login'))
            ->assertRedirect(route('admin.dashboard'));
    }

    // DN08: chưa đăng nhập mà vào trang cần đăng nhập
    public static function protectedUrls(): array
    {
        return [
            'gio_hang' => ['/cart'],
            'don_hang' => ['/orders'],
            'ho_so'    => ['/profile'],
            'trang_chu_user' => ['/home'],
            'admin'    => ['/admin'],
        ];
    }

    #[DataProvider('protectedUrls')]
    public function test_DN08_guest_is_redirected_to_login(string $url): void
    {
        $this->get($url)->assertRedirect(route('login'));
    }

    // DN09: user thường mở trang admin
    public function test_DN09_normal_user_cannot_open_admin_area(): void
    {
        $this->actingAs($this->createUser('user'))->get('/admin')->assertForbidden();
    }

    // DN10: trang đăng nhập và đăng ký hiển thị được, ô mật khẩu được che
    public function test_DN10_login_and_register_pages_are_available_with_masked_password(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('type="password"', false);
        $this->get(route('register'))->assertOk()->assertSee('type="password"', false);
    }

    // DN11: đăng xuất
    public function test_DN11_logout_ends_the_session(): void
    {
        $this->actingAs($this->createUser())->post(route('logout'))->assertRedirect(route('login'));

        $this->assertGuest();
        $this->get(route('cart'))->assertRedirect(route('login'));
    }

    // DN12: danh sách sản phẩm công khai, không cần đăng nhập
    public function test_DN12_product_list_is_public(): void
    {
        $product = $this->createProduct();

        $this->get(route('products.index'))->assertOk();
        $this->get(route('products.show', $product->id))->assertOk()->assertSee($product->name);
    }

    // DN13: sản phẩm không tồn tại
    public function test_DN13_missing_product_returns_404(): void
    {
        $this->get(route('products.show', 999999))->assertNotFound();
    }
}