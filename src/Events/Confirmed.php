<?php

namespace BeyondCode\EmailConfirmation\Events;

use Illuminate\Queue\SerializesModels;

/**
 * Class Confirmed
 * @package BeyondCode\EmailConfirmation\Events
 */
class Confirmed
{
    use SerializesModels;

    /**
     * The authenticated user.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}