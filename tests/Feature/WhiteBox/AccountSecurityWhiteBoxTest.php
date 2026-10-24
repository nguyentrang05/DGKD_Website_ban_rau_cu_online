<?php

namespace Tests\Feature\WhiteBox;

use App\Http\Middleware\AdminMiddleware;
use App\Models\User;
use Illuminate\Http\Request;

class AccountSecurityWhiteBoxTest extends ExtendedWhiteBoxTestCase
{
    public function test_SA01_nonexistent_email_is_rejected(): void
    {
        $this->from(route('login'))->post(route('login.post'), [
            'email' => 'missing@example.com', 'password' => self::PASSWORD,
        ])->assertRedirect(route('login'))->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_SA02_missing_email_is_rejected(): void
    {
        $this->postJson(route('login.post'), ['password' => self::PASSWORD])
            ->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertGuest();
    }

    public function test_SA03_both_credentials_empty_are_rejected(): void
    {
        $this->postJson(route('login.post'), ['email' => '', 'password' => ''])
            ->assertUnprocessable()->assertJsonValidationErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_SA04_newly_registered_account_can_logout_and_login_again(): void
    {
        $data = $this->registrationData();
        $this->post(route('register.post'), $data)->assertRedirect(route('home'));
        $user = User::where('email', $data['email'])->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
        $this->post(route('login.post'), [
            'email' => $data['email'], 'password' => $data['password'],
        ])->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_SA05_normal_user_cannot_open_admin_dashboard(): void
    {
        $this->actingAs($this->makeUser())->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_SA06_guest_cannot_open_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    // Isolate the middleware acceptance branch from unrelated dashboard logic.
    public function test_SA07_admin_middleware_calls_next_for_admin(): void
    {
        $this->actingAs($this->makeUser('admin'));
        $called = false;
        $response = (new AdminMiddleware())->handle(Request::create('/admin'), function () use (&$called) {
            $called = true;
            return response('admin-accepted', 200);
        });
        $this->assertTrue($called);
        $this->assertSame('admin-accepted', $response->getContent());
    }

    public function test_SA08_logout_removes_authentication_and_blocks_cart(): void
    {
        $this->actingAs($this->makeUser())->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
        $this->get(route('cart'))->assertRedirect(route('login'));
    }

    public function test_SA09_guest_logout_is_redirected_by_login_middleware(): void
    {
        $this->post(route('logout'))->assertRedirect(route('login'))->assertSessionHasErrors('email');
    }

    public function test_SA10_authenticated_user_is_redirected_away_from_login(): void
    {
        $this->actingAs($this->makeUser())->get(route('login'))->assertRedirect(route('home'));
    }

    public function test_SA11_authenticated_admin_is_redirected_away_from_registration(): void
    {
        $this->actingAs($this->makeUser('admin'))->get(route('register'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_SA12_login_password_input_is_masked_in_rendered_html(): void
    {
        $xpath = $this->htmlXPath($this->get(route('login')));
        $this->assertSame(1, $xpath->query('//input[@name="password" and @type="password"]')->length);
    }

    public function test_SA13_registration_password_and_confirmation_are_masked(): void
    {
        $xpath = $this->htmlXPath($this->get(route('register')));
        foreach (['password', 'password_confirmation'] as $name) {
            $this->assertSame(1, $xpath->query('//input[@name="'.$name.'" and @type="password"]')->length);
        }
    }
}
