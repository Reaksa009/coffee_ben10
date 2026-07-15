<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_succeeds_with_valid_password(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Pass@$#12345678',
            'password_confirmation' => 'Pass@$#12345678',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
        $this->assertAuthenticated();
    }

    public function test_registration_fails_missing_at_symbol(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Pass$#12345678',
            'password_confirmation' => 'Pass$#12345678',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_registration_fails_missing_dollar_symbol(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Pass@#12345678',
            'password_confirmation' => 'Pass@#12345678',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_registration_fails_missing_hash_symbol(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Pass@$12345678',
            'password_confirmation' => 'Pass@$12345678',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_registration_fails_insufficient_digits(): void
    {
        // 7 digits: 1234567
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Pass@$#1234567',
            'password_confirmation' => 'Pass@$#1234567',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);
    }
}
