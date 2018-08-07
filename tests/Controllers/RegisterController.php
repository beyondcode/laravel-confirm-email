<?php

namespace BeyondCode\EmailConfirmation\Tests\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Bus\DispatchesJobs;
use BeyondCode\EmailConfirmation\Tests\Models\User;
use Illuminate\Foundation\Validation\ValidatesRequests;
use BeyondCode\EmailConfirmation\Traits\RegistersUsers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, RegistersUsers;

    protected $redirectConfirmationTo = 'redirectConfirmationUrl';

    protected $redirectAfterRegistrationTo = 'redirectAfterRegistration';

    protected $redirectAfterResendConfirmationTo = 'redirectAfterConfirmationResent';

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \User
     */
    protected function create(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}