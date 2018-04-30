<?php

namespace BeyondCode\EmailConfirmation\Tests;

use Illuminate\Support\Facades\Notification;
use BeyondCode\EmailConfirmation\Tests\Models\User;
use BeyondCode\EmailConfirmation\Notifications\ConfirmEmail;

class ConfirmationTest extends TestCase
{

    /** @test */
    public function it_adds_confirmation_codes_to_registered_users()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'email' => 'marcel@beyondco.de',
            'password' => 'test123',
            'password_confirmation' => 'test123'
        ]);

        $user = User::whereEmail('marcel@beyondco.de')->first();

        $this->assertNull($user->confirmed_at);
        $this->assertNotNull($user->confirmation_code);

        $response->assertSessionHas('confirmation', __('confirmation::confirmation.confirmation_info'));

        Notification::assertSentTo($user, ConfirmEmail::class);
    }

    /** @test */
    public function it_does_not_allow_login_for_unconfirmed_users()
    {
        $user = User::create([
            'email' => 'marcel@beyondco.de',
            'password' => bcrypt('test123'),
            'confirmed_at' => null
        ]);

        $response = $this->post('/login', [
            'email' => 'marcel@beyondco.de',
            'password' => 'test123'
        ]);

        $response->assertSessionHas('confirmation_user_id', $user->getKey());
        $response->assertSessionHasErrors('confirmation');
        $this->assertFalse(auth()->check());
    }

    /** @test */
    public function it_allows_login_for_confirmed_users()
    {
        User::create([
            'email' => 'marcel@beyondco.de',
            'password' => bcrypt('test123'),
            'confirmed_at' => now()
        ]);

        $response = $this->post('/login', [
            'email' => 'marcel@beyondco.de',
            'password' => 'test123'
        ]);

        $response->assertRedirect('/home');
        $this->assertTrue(auth()->check());
    }

    /** @test */
    public function it_can_resend_confirmation_codes()
    {
        Notification::fake();

        $user = User::create([
            'email' => 'marcel@beyondco.de',
            'password' => bcrypt('test123'),
            'confirmed_at' => null
        ]);

        $this->post('/login', [
            'email' => 'marcel@beyondco.de',
            'password' => 'test123'
        ]);

        $response = $this->get('/resend');

        $response->assertSessionHas('confirmation', __('confirmation::confirmation.confirmation_resent'));

        Notification::assertSentTo($user, ConfirmEmail::class);
    }

    /** @test */
    public function it_can_confirm_valid_codes()
    {
        Notification::fake();

        User::create([
            'email' => 'marcel@beyondco.de',
            'password' => bcrypt('test123'),
            'confirmed_at' => null,
            'confirmation_code' => 'abcdefg'
        ]);

        $response = $this->get('/register/confirm/abcdefg');

        $response->assertSessionHas('confirmation', __('confirmation::confirmation.confirmation_successful'));

        $response->assertRedirect('/login');
    }

    /** @test */
    public function it_returns_404_for_invalid_codes()
    {
        Notification::fake();

        $response = $this->get('/register/confirm/foo');

        $response->assertStatus(404);
    }
}
