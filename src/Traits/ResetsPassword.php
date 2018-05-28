<?php

namespace BeyondCode\EmailConfirmation\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

trait ResetsPasswords
{
    use \Illuminate\Foundation\Auth\ResetsPasswords;

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        $user = $this->broker()->getUser($this->credentials($request));

        // If the user hasn't confirmed their email address,
        // we will redirect them back with their error message.
        if (is_null($user->confirmed_at)) {

            session(['confirmation_user_id' => $user->getKey()]);

            return redirect()->back()->with(
                'confirmation', __('confirmation::confirmation.not_confirmed', [
                    'resend_link' => route('auth.resend_confirmation')
                ])
            );
        }

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($response)
            : $this->sendResetFailedResponse($request, $response);
    }
}