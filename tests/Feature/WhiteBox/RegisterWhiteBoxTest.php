<?php

namespace Tests\Feature\WhiteBox;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;

class RegisterWhiteBoxTest extends ExtendedWhiteBoxTestCase
{
    public function test_R01_valid_registration_creates_user_hashes_password_and_logs_in(): void
    {
        $data = $this->registrationData();
        $this->post(route('register.post'), $data)
            ->assertRedirect(route('home'))->assertSessionHasNoErrors();

        $this->assertDatabaseCount('users', 1);
        $user = User::where('email', $data['email'])->firstOrFail();
        $this->assertSame($data['name'], $user->name);
        $this->assertSame('user', $user->role);
        $this->assertNotSame($data['password'], $user->password);
        $this->assertTrue(Hash::check($data['password'], $user->password));
        $this->assertAuthenticatedAs($user);
    }

    public static function requiredFields(): array
    {
        return ['R02_name' => ['name'], 'R02_email' => ['email'], 'R02_password' => ['password']];
    }

    #[DataProvider('requiredFields')]
    public function test_R02_backend_required_fields_are_validated(string $field): void
    {
        $data = $this->registrationData();
        unset($data[$field]);
        $this->postJson(route('register.post'), $data)
            ->assertUnprocessable()->assertJsonValidationErrors($field);
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_R03_duplicate_email_is_rejected_without_changing_existing_user(): void
    {
        $existing = $this->makeUser('user', 'register.wb@example.com');
        $before = $existing->getRawOriginal('password');
        $this->postJson(route('register.post'), $this->registrationData())
            ->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertDatabaseCount('users', 1);
        $this->assertSame($before, $existing->fresh()->getRawOriginal('password'));
        $this->assertGuest();
    }

    public function test_R04_malformed_email_is_rejected(): void
    {
        $this->postJson(route('register.post'), $this->registrationData(['email' => 'invalid-email']))
            ->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_R05_password_with_seven_characters_is_rejected(): void
    {
        $data = $this->registrationData(['password' => 'Abc123!', 'password_confirmation' => 'Abc123!']);
        $this->postJson(route('register.post'), $data)
            ->assertUnprocessable()->assertJsonValidationErrors('password');
        $this->assertDatabaseCount('users', 0);
    }

    public static function validPasswordBoundaries(): array
    {
        return ['R06_eight' => ['Abc123!x'], 'R06_nine' => ['Abc123!xy']];
    }

    #[DataProvider('validPasswordBoundaries')]
    public function test_R06_password_at_and_above_minimum_is_accepted(string $password): void
    {
        $this->post(route('register.post'), $this->registrationData([
            'password' => $password, 'password_confirmation' => $password,
        ]))->assertRedirect(route('home'))->assertSessionHasNoErrors();
        $user = User::firstOrFail();
        $this->assertTrue(Hash::check($password, $user->password));
        $this->assertAuthenticatedAs($user);
    }

    public function test_R07_mismatched_confirmation_is_rejected(): void
    {
        $this->postJson(route('register.post'), $this->registrationData([
            'password_confirmation' => 'Different@123',
        ]))->assertUnprocessable()->assertJsonValidationErrors('password');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_R08_missing_confirmation_is_rejected(): void
    {
        $data = $this->registrationData();
        unset($data['password_confirmation']);
        $this->postJson(route('register.post'), $data)
            ->assertUnprocessable()->assertJsonValidationErrors('password');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_R09_name_over_255_characters_is_rejected(): void
    {
        $this->postJson(route('register.post'), $this->registrationData(['name' => str_repeat('A', 256)]))
            ->assertUnprocessable()->assertJsonValidationErrors('name');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_R10_client_cannot_register_itself_as_admin(): void
    {
        $this->post(route('register.post'), $this->registrationData(['role' => 'admin']))
            ->assertRedirect(route('home'));
        $this->assertSame('user', User::firstOrFail()->role);
    }
}
