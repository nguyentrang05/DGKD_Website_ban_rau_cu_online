<?php

namespace Tests\Feature\WhiteBox;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;

class RegisterRequirementsTest extends ExtendedWhiteBoxTestCase
{
    public static function invalidFormData(): array
    {
        return [
            'RD01_missing_required_phone' => ['phone', null],
            'RD02_non_numeric_phone' => ['phone', 'abc-not-a-phone'],
            'RD03_unaccepted_required_terms' => ['terms', null],
        ];
    }

    // phone and terms are marked required in the actual registration form.
    // The PDF explicitly requests validation of the phone format.
    #[DataProvider('invalidFormData')]
    public function test_registration_form_requirements_are_enforced_by_server(string $field, mixed $value): void
    {
        $data = $this->registrationData();
        if ($value === null) {
            unset($data[$field]);
        } else {
            $data[$field] = $value;
        }
        $response = $this->postJson(route('register.post'), $data);
        $this->assertSame(422, $response->getStatusCode(), sprintf(
            'field=%s; HTTP=%d; created users=%d. Expected validation before creating account.',
            $field, $response->getStatusCode(), User::count()
        ));
        $response->assertJsonValidationErrors($field);
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_RD04_valid_phone_and_address_are_saved_from_registration_form(): void
    {
        $data = $this->registrationData();
        $this->post(route('register.post'), $data)->assertRedirect(route('home'));
        $user = User::where('email', $data['email'])->firstOrFail();
        $this->assertSame(
            ['phone' => $data['phone'], 'address' => $data['address']],
            ['phone' => $user->phone, 'address' => $user->address],
            'Registration accepted data but did not persist phone/address.'
        );
    }
}
