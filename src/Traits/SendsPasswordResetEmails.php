<?php

namespace BeyondCode\EmailConfirmation\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

/**
 * Trait SendsPasswordResetEmails
 * @package BeyondCode\EmailConfirmation\Traits
 */
trait SendsPasswordResetEmails
{
    use \Illuminate\Foundation\Auth\SendsPasswordResetEmails;

    /**
     * Send a reset link to the given user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws ValidationException
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $user = $this->broker()->getUser($request->only('email'));

        // If the user hasn't confirmed their email address,
        // we will throw a validation exception for this error.
        // A user can not request a password reset link if they are not confirmed.
        if (is_null($user->confirmed_at)) {

            session(['confirmation_user_id' => $user->getKey()]);

            throw ValidationException::withMessages([
                'confirmation' => [
                    __('confirmation::confirmation.not_confirmed_reset_password', [
                        'resend_link' => route('auth.resend_confirmation')
                    ])
                ]
            ]);
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }
}