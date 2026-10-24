<?php

namespace Tests\Feature\BlackBox;

use PHPUnit\Framework\Attributes\DataProvider;

class RegisterBlackBoxTest extends BlackBoxTestCase
{
    private function submit(array $overrides = [], array $remove = [])
    {
        $data = array_replace($this->validRegistration(), $overrides);
        foreach ($remove as $key) {
            unset($data[$key]);
        }

        return $this->from(route('register'))->post(route('register.post'), $data);
    }

    private function assertRejected($response, string $field): void
    {
        $response->assertRedirect(route('register'))->assertSessionHasErrors($field);
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    // DK01: dữ liệu hợp lệ
    public function test_DK01_valid_data_creates_account_and_logs_in(): void
    {
        $this->submit()->assertRedirect(route('home'))->assertSessionHasNoErrors();

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'nguyenvana@example.com', 'role' => 'user']);
    }

    // DK02: thiếu từng trường bắt buộc
    public static function requiredFields(): array
    {
        return [
            'DK02_thieu_ho_ten'   => ['name'],
            'DK02_thieu_email'    => ['email'],
            'DK02_thieu_sdt'      => ['phone'],
            'DK02_thieu_mat_khau' => ['password'],
            'DK12_thieu_dieu_khoan' => ['terms'],
        ];
    }

    #[DataProvider('requiredFields')]
    public function test_DK02_missing_required_field_is_rejected(string $field): void
    {
        $this->assertRejected($this->submit([], [$field]), $field);
    }

    // DK02b: họ tên chỉ có khoảng trắng
    public function test_DK02b_name_with_only_spaces_is_rejected(): void
    {
        $this->assertRejected($this->submit(['name' => '   ']), 'name');
    }

    // DK03: email sai định dạng
    public static function invalidEmails(): array
    {
        return [
            'thieu_@'        => ['abc.com'],
            'thieu_ten_mien' => ['abc@'],
            'thieu_phan_ten' => ['@abc.com'],
            'co_khoang_trang' => ['a b@c.com'],
        ];
    }

    #[DataProvider('invalidEmails')]
    public function test_DK03_invalid_email_is_rejected(string $email): void
    {
        $this->assertRejected($this->submit(['email' => $email]), 'email');
    }

    // DK04: email đã tồn tại
    public function test_DK04_duplicate_email_is_rejected(): void
    {
        $this->createUser('user', 'nguyenvana@example.com');

        $this->submit()->assertRedirect(route('register'))->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    // DK05: mật khẩu 7 ký tự (biên dưới - 1)
    public function test_DK05_password_with_seven_characters_is_rejected(): void
    {
        $this->assertRejected(
            $this->submit(['password' => 'Abc123!', 'password_confirmation' => 'Abc123!']),
            'password'
        );
    }

    // DK06-DK07: mật khẩu 8 và 9 ký tự (biên dưới và trên biên)
    public static function validPasswords(): array
    {
        return ['DK06_8_ky_tu' => ['Abc123!x'], 'DK07_9_ky_tu' => ['Abc123!xy']];
    }

    #[DataProvider('validPasswords')]
    public function test_DK06_password_at_or_above_minimum_is_accepted(string $password): void
    {
        $this->submit(['password' => $password, 'password_confirmation' => $password])
            ->assertRedirect(route('home'));

        $this->assertAuthenticated();
    }

    // DK08: xác nhận mật khẩu không khớp
    public function test_DK08_password_confirmation_mismatch_is_rejected(): void
    {
        $this->assertRejected($this->submit(['password_confirmation' => 'KhacNhau@123']), 'password');
    }

    // DK09: họ tên đúng 255 ký tự được chấp nhận
    public function test_DK09_name_with_255_characters_is_accepted(): void
    {
        $this->submit(['name' => str_repeat('A', 255)])->assertRedirect(route('home'));

        $this->assertAuthenticated();
    }

    // DK10: họ tên 256 ký tự bị từ chối
    public function test_DK10_name_with_256_characters_is_rejected(): void
    {
        $this->assertRejected($this->submit(['name' => str_repeat('A', 256)]), 'name');
    }

    // DK11: số điện thoại sai định dạng
    public static function invalidPhones(): array
    {
        return [
            'chu_cai'      => ['abc-xyz'],
            'qua_ngan'     => ['12345'],
            'thieu_so_0'   => ['901234567'],
            'qua_dai'      => ['090123456789012'],
            'co_ky_tu_la'  => ['0901-234-567'],
        ];
    }

    #[DataProvider('invalidPhones')]
    public function test_DK11_invalid_phone_is_rejected(string $phone): void
    {
        $this->assertRejected($this->submit(['phone' => $phone]), 'phone');
    }

    // DK11b: số điện thoại hợp lệ
    public static function validPhones(): array
    {
        return [
            'dau_0_di_dong' => ['0901234567'],
            'dau_84'        => ['+84901234567'],
            'may_ban'       => ['0281234567'],
        ];
    }

    #[DataProvider('validPhones')]
    public function test_DK11b_valid_phone_is_accepted(string $phone): void
    {
        $this->submit(['phone' => $phone])->assertRedirect(route('home'));

        $this->assertAuthenticated();
    }

    // DK13: số điện thoại và địa chỉ phải hiển thị trong hồ sơ sau khi đăng ký
    public function test_DK13_phone_and_address_are_saved(): void
    {
        $this->submit(['phone' => '0987654321', 'address' => 'So 1 Dai Co Viet'])
            ->assertRedirect(route('home'));

        $this->get(route('profile'))->assertOk()->assertSee('0987654321');
        $this->assertDatabaseHas('users', ['email' => 'nguyenvana@example.com', 'address' => 'So 1 Dai Co Viet']);
    }

    // DK14: không thể tự đăng ký làm admin bằng cách gửi thêm trường role
    public function test_DK14_client_cannot_choose_admin_role(): void
    {
        $this->submit(['role' => 'admin'])->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', ['email' => 'nguyenvana@example.com', 'role' => 'user']);
    }

    // DK15: mật khẩu không được lưu dưới dạng văn bản thường
    public function test_DK15_password_is_not_stored_in_plain_text(): void
    {
        $this->submit()->assertRedirect(route('home'));

        $this->assertDatabaseMissing('users', ['password' => self::PASSWORD]);
    }
}