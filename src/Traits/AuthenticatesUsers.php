<?php

namespace BeyondCode\EmailConfirmation\Traits;


use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait AuthenticatesUsers
{
    use \Illuminate\Foundation\Auth\AuthenticatesUsers {
        attemptLogin as baseAttemptLogin;
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     * @throws ValidationException
     */
    protected function attemptLogin(Request $request)
    {
        if ($this->guard()->validate($this->credentials($request))) {
            $user = $this->guard()->getLastAttempted();

            if (! is_null($user->confirmed_at)) {
                return $this->baseAttemptLogin($request);
            }

            session([
                'confirmation_user_id' => $user->getKey()
            ]);

            throw ValidationException::withMessages([
                'confirmation' => [
                    __('confirmation::confirmation.not_confirmed', [
                        'resend_link' => route('auth.resend_confirmation')
                    ])
                ]
            ]);
        }
        return false;
    }
}