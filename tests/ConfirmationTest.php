<?php

namespace BeyondCode\EmailConfirmation\Tests;

use BeyondCode\EmailConfirmation\Events\Confirmed;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use BeyondCode\EmailConfirmation\Tests\Models\User;
use BeyondCode\EmailConfirmation\Notifications\ConfirmEmail;
use Illuminate\Support\Facades\URL;

class ConfirmationTest extends TestCase
{

    /** @test */
    public function it_sends_confirmation_links_to_registered_users()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'email' => 'marcel@beyondco.de',
            'password' => 'test123',
            'password_confirmation' => 'test123'
        ]);

        $user = User::whereEmail('marcel@beyondco.de')->first();

        $this->assertNull($user->confirmed_at);

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
    public function it_can_resend_confirmation_links()
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

        $response = $this->get('/register/resend_confirmation');

        $response->assertSessionHas('confirmation', __('confirmation::confirmation.confirmation_resent'));

        Notification::assertSentTo($user, ConfirmEmail::class);
    }

    /** @test */
    public function it_can_confirm_valid_links()
    {
        Notification::fake();

        $user = User::create([
            'email' => 'marcel@beyondco.de',
            'password' => bcrypt('test123'),
            'confirmed_at' => null,
        ]);

        $response = $this->get($this->getConfirmationUrl($user->getKey()));

        $response->assertSessionHas('confirmation', __('confirmation::confirmation.confirmation_successful'));

        $response->assertRedirect('/login');
    }

    /** @test */
    public function it_returns_403_for_invalid_confirmation_links()
    {

        $user = User::create([
            'email' => 'marcel@beyondco.de',
            'password' => bcrypt('test123'),
            'confirmed_at' => null,
        ]);

        $response = $this->get(route("auth.confirm", ['id' => $user->getKey()])."?signature=haha");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_does_not_allow_reset_password_request_for_unconfirmed_users()
    {
        Notification::fake();

        $user = User::create([
            'email' => 'marcel@beyondco.de',
            'password' => bcrypt('test123'),
            'confirmed_at' => null,
        ]);

        $response = $this->post('/password/email', [
            'email' => 'marcel@beyondco.de',
        ]);

        $response->assertSessionHas('confirmation_user_id', $user->getKey());

        $response->assertSessionHasErrors('confirmation');

        Notification::assertNotSentTo($user, ResetPassword::class);
    }

    /** @test */
    public function it_dispatches_confirmed_event_on_successful_confirmation()
    {
        Event::fake();

        $user = User::create([
            'email' => 'marcel@beyondco.de',
            'password' => bcrypt('test123'),
            'confirmed_at' => null,
        ]);

        $response = $this->get($this->getConfirmationUrl($user->getKey()));

        Event::assertDispatched(Confirmed::class, function ($e) use ($user) {
            return $e->user->email === $user->email;
        });

        $response->assertRedirect('/login');
    }

    /** @test */
    public function it_does_not_dispatch_confirmed_event_on_failed_confirmation()
    {
        Event::fake();

        $user = User::create([
            'email' => 'marcel@beyondco.de',
            'password' => bcrypt('test123'),
            'confirmed_at' => null,
        ]);

        $response = $this->get(route("auth.confirm", ['id' => $user->getKey()])."?signature=haha");

        Event::assertNotDispatched(Confirmed::class);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_invalidates_confirmation_link_after_set_timeout()
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

        $response = $this->get('/register/resend_confirmation');

        $response->assertSessionHas('confirmation', __('confirmation::confirmation.confirmation_resent'));

        Notification::assertSentTo($user, ConfirmEmail::class);
    }

    protected function getConfirmationUrl($id)
    {
        return URL::signedRoute("auth.confirm", ['id' => $id]);
    }
}
