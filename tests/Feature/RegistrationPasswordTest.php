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

    public function test_registration_fails_missing_lowercase_letter(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'PASS@$#12345678',
            'password_confirmation' => 'PASS@$#12345678',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_registration_fails_missing_uppercase_letter(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'pass@$#12345678',
            'password_confirmation' => 'pass@$#12345678',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_registration_succeeds_with_only_at_symbol(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john1@example.com',
            'password' => 'Pass@12345678',
            'password_confirmation' => 'Pass@12345678',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => 'john1@example.com',
        ]);
    }

    public function test_registration_succeeds_with_only_dollar_symbol(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john2@example.com',
            'password' => 'Pass$12345678',
            'password_confirmation' => 'Pass$12345678',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => 'john2@example.com',
        ]);
    }

    public function test_registration_succeeds_with_only_hash_symbol(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john3@example.com',
            'password' => 'Pass#12345678',
            'password_confirmation' => 'Pass#12345678',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => 'john3@example.com',
        ]);
    }

    public function test_registration_fails_missing_all_required_symbols(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Pass%12345678', // has a symbol but not @, $, or #
            'password_confirmation' => 'Pass%12345678',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_registration_fails_insufficient_length(): void
    {
        // 7 characters: Pas@123
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Pas@123',
            'password_confirmation' => 'Pas@123',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);
    }
}
