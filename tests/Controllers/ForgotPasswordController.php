<?php

namespace BeyondCode\EmailConfirmation\Tests\Controllers;

use BeyondCode\EmailConfirmation\Traits\SendsPasswordResetEmails;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, SendsPasswordResetEmails;
}