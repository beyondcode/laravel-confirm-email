<?php

namespace BeyondCode\EmailConfirmation\Traits;

use BeyondCode\EmailConfirmation\Events\Confirmed;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

trait RegistersUsers
{
    use \Illuminate\Foundation\Auth\RegistersUsers {
        register as baseRegister;
    }

    /**
     * Confirm a user with a given confirmation code.
     *
     * @param $confirmation_code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm(Request $request, $id)
    {
        $model = $this->guard()->getProvider()->createModel();
        $user = $model->where('id', $id)->firstOrFail();

        $user->confirmed_at = now();
        $user->save();

        event(new Confirmed($user));

        return $this->confirmed($user)
            ?: redirect(route('login'))->with('confirmation', __('confirmation::confirmation.confirmation_successful'));
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

        return redirect(route('login'))->with('confirmation', __('confirmation::confirmation.confirmation_resent'));
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
            ?: redirect(route('login'))->with('confirmation', __('confirmation::confirmation.confirmation_info'));
    }

    /**
     * Send the confirmation code to a user.
     *
     * @param $user
     */
    protected function sendConfirmationToUser($user)
    {
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
