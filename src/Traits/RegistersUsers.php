<?php

namespace BeyondCode\EmailConfirmation\Traits;

use BeyondCode\EmailConfirmation\Events\Confirmed;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;

trait RegistersUsers
{
    use \Illuminate\Foundation\Auth\RegistersUsers {
        register as baseRegister;
    }

    /**
     * Get redirect path after a successful confirmation.
     *
     * @return string
     */
    public function redirectAfterConfirmationPath()
    {
        if (method_exists($this, 'redirectConfirmationTo')) {
            return $this->redirectConfirmationTo();
        }

        return property_exists($this, 'redirectConfirmationTo') ? $this->redirectConfirmationTo : route('login');
    }

    /**
     * Get redirect path after a registration that still needs to be confirmed.
     *
     * @return string
     */
    public function redirectAfterRegistrationPath()
    {
        if (method_exists($this, 'redirectAfterRegistrationTo')) {
            return $this->redirectAfterRegistrationTo();
        }

        return property_exists($this, 'redirectAfterRegistrationTo') ? $this->redirectAfterRegistrationTo : route('login');
    }

    /**
     * Get redirect path after the confirmation was sent.
     *
     * @return string
     */
    public function redirectAfterResendConfirmationPath()
    {
        if (method_exists($this, 'redirectAfterResendConfirmationTo')) {
            return $this->redirectAfterResendConfirmationTo();
        }

        return property_exists($this, 'redirectAfterResendConfirmationTo') ? $this->redirectAfterResendConfirmationTo : route('login');
    }

    /**
     * Confirm a user with a given confirmation code.
     *
     * @param $confirmation_code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm($confirmation_code)
    {
        $model = $this->guard()->getProvider()->createModel();

        $user = $model->where('confirmation_code', $confirmation_code)->firstOrFail();

        $user->confirmation_code = null;
        $user->confirmed_at = now();
        $user->save();

        event(new Confirmed($user));

        return $this->confirmed($user)
            ?: redirect($this->redirectAfterConfirmationPath())->with('confirmation', __('confirmation::confirmation.confirmation_successful'));
    }

    /**
     * Resend a confirmation code to a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendConfirmation(Request $request)
    {
        $model = $this->guard()->getProvider()->createModel();

        $user = $model->findOrFail($request->session()->pull('confirmation_user_id'));
        $this->sendConfirmationToUser($user);

        return redirect($this->redirectAfterResendConfirmationPath())->with('confirmation', __('confirmation::confirmation.confirmation_resent'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->sendConfirmationToUser($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectAfterRegistrationPath())->with('confirmation', __('confirmation::confirmation.confirmation_info'));
    }

    /**
     * Send the confirmation code to a user.
     *
     * @param $user
     */
    protected function sendConfirmationToUser($user)
    {
        // Create the confirmation code
        $user->confirmation_code = Str::random(25);
        $user->save();

        // Notify the user
        $notification = app(config('confirmation.notification'));
        $user->notify($notification);
    }


    /**
     * The users email address has been confirmed.
     *
     * @param  mixed  $user
     * @return mixed
     */
    protected function confirmed($user) {
        //
    }
}
