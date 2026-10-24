<?php

namespace Tests\Feature\BlackBox;

use PHPUnit\Framework\Attributes\DataProvider;

class ProfileBlackBoxTest extends BlackBoxTestCase
{
    private function update(array $overrides = [], array $remove = [])
    {
        $data = array_replace(['name' => 'Ten Moi', 'phone' => '0912345678'], $overrides);
        foreach ($remove as $key) {
            unset($data[$key]);
        }

        return $this->from(route('profile'))->post(route('profile.update'), $data);
    }

    // HS01: cập nhật hợp lệ
    public function test_HS01_valid_update_is_saved_and_shown(): void
    {
        $this->actingAs($this->createUser());

        $this->update()->assertRedirect(route('profile'))->assertSessionHas('success');

        $this->get(route('profile'))->assertOk()->assertSee('Ten Moi')->assertSee('0912345678');
    }

    // HS02: thiếu họ tên hoặc số điện thoại
    public static function requiredFields(): array
    {
        return ['thieu_ho_ten' => ['name'], 'thieu_sdt' => ['phone']];
    }

    #[DataProvider('requiredFields')]
    public function test_HS02_missing_required_field_is_rejected(string $field): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $this->update([], [$field])->assertSessionHasErrors($field);

        $this->assertSame($user->name, $user->fresh()->name);
    }

    // HS03: độ dài số điện thoại tại biên 20 và 21
    public function test_HS03_phone_with_20_characters_is_accepted(): void
    {
        $this->actingAs($this->createUser());

        $this->update(['phone' => str_repeat('1', 20)])->assertSessionHasNoErrors();
    }

    public function test_HS03b_phone_with_21_characters_is_rejected(): void
    {
        $this->actingAs($this->createUser());

        $this->update(['phone' => str_repeat('1', 21)])->assertSessionHasErrors('phone');
    }

    // HS04: ngày sinh phải là ngày trong quá khứ
    public function test_HS04_birthday_yesterday_is_accepted(): void
    {
        $this->actingAs($this->createUser());

        $this->update(['birthday' => now()->subDay()->format('Y-m-d')])->assertSessionHasNoErrors();
    }

    public static function invalidBirthdays(): array
    {
        return [
            'hom_nay'    => [0],
            'ngay_mai'   => [1],
            'nam_sau'    => [365],
        ];
    }

    #[DataProvider('invalidBirthdays')]
    public function test_HS04b_birthday_today_or_future_is_rejected(int $daysFromToday): void
    {
        $this->actingAs($this->createUser());

        $this->update(['birthday' => now()->addDays($daysFromToday)->format('Y-m-d')])
            ->assertSessionHasErrors('birthday');
    }

    // HS04c: ngày sinh sai định dạng
    public function test_HS04c_malformed_birthday_is_rejected(): void
    {
        $this->actingAs($this->createUser());

        $this->update(['birthday' => 'khong-phai-ngay'])->assertSessionHasErrors('birthday');
    }

    // HS05: giới tính chỉ nhận male / female / other
    public static function validGenders(): array
    {
        return ['nam' => ['male'], 'nu' => ['female'], 'khac' => ['other']];
    }

    #[DataProvider('validGenders')]
    public function test_HS05_valid_gender_is_accepted(string $gender): void
    {
        $this->actingAs($this->createUser());

        $this->update(['gender' => $gender])->assertSessionHasNoErrors();
    }

    public function test_HS05b_unknown_gender_is_rejected(): void
    {
        $this->actingAs($this->createUser());

        $this->update(['gender' => 'abc'])->assertSessionHasErrors('gender');
    }

    // HS06: không đổi được email hoặc quyền qua form hồ sơ
    public function test_HS06_profile_form_cannot_change_email_or_role(): void
    {
        $user = $this->createUser('user', 'giu-nguyen@example.com');
        $this->actingAs($user);

        $this->update(['email' => 'doi-email@example.com', 'role' => 'admin']);

        $fresh = $user->fresh();
        $this->assertSame('giu-nguyen@example.com', $fresh->email);
        $this->assertSame('user', $fresh->role);
    }

    // HS07: khách chưa đăng nhập
    public function test_HS07_guest_cannot_open_or_update_profile(): void
    {
        $this->get(route('profile'))->assertRedirect(route('login'));
        $this->post(route('profile.update'), ['name' => 'A', 'phone' => '0912345678'])
            ->assertRedirect(route('login'));
    }
}