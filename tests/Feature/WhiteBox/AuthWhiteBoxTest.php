<?php

namespace Tests\Feature\WhiteBox;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthWhiteBoxTest extends WhiteBoxTestCase
{
    // L1: attempt=false -> authentication error.
    public function test_L1_wrong_password_is_rejected(): void
    {
        $user = $this->makeUser();

        $this->from(route('login'))->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'WrongPassword',
        ])->assertRedirect(route('login'))
          ->assertSessionHasErrors(['email' => 'Sai email hoặc mật khẩu.']);

        $this->assertGuest();
    }

    // L2: attempt=true -> isAdmin=true -> admin dashboard.
    public function test_L2_admin_is_redirected_to_dashboard(): void
    {
        $user = $this->makeUser('admin');

        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => self::PASSWORD,
        ])->assertRedirect(route('admin.dashboard'))
          ->assertSessionHasNoErrors();

        $this->assertAuthenticatedAs($user);
    }

    // L3: attempt=true -> isAdmin=false -> isUser=true -> home.
    public function test_L3_user_is_redirected_to_home(): void
    {
        $user = $this->makeUser();

        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => self::PASSWORD,
        ])->assertRedirect(route('home'))
          ->assertSessionHasNoErrors();

        $this->assertAuthenticatedAs($user);
    }

    // L4: defensive path. Schema only permits user/admin, so simulate auth state.
    // This checks controller behavior with a mock, NOT a real third database role.
    public function test_L4_unsupported_role_is_logged_out_with_mocked_auth(): void
    {
        $credentials = ['email' => 'unsupported@example.com', 'password' => self::PASSWORD];
        $account = new User(['role' => 'unsupported']); // Never persisted.

        Auth::shouldReceive('check')->andReturn(false); // Guest-only middleware.
        Auth::shouldReceive('attempt')->once()->with($credentials)->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn($account);
        Auth::shouldReceive('logout')->once()->andReturnNull();

        $this->from(route('login'))->post(route('login.post'), $credentials)
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Tài khoản không có quyền truy cập.']);
    }

    // Extra validation path; not counted as an explicit if in the reduced CFG.
    public function test_L5_invalid_email_is_rejected_before_authentication(): void
    {
        $this->from(route('login'))->post(route('login.post'), [
            'email' => 'not-an-email',
            'password' => self::PASSWORD,
        ])->assertRedirect(route('login'))->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_L6_missing_password_is_rejected_before_authentication(): void
    {
        $this->from(route('login'))->post(route('login.post'), [
            'email' => 'wb@example.com',
        ])->assertRedirect(route('login'))->assertSessionHasErrors('password');

        $this->assertGuest();
    }
}
