<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Confirmation URL timeout (in minutes)
    |--------------------------------------------------------------------------
    |
    | After this time has elapsed, the confirmation URL will no longer work
    |
    */
    'timeout' => 60,

    /*
    |--------------------------------------------------------------------------
    | Notification
    |--------------------------------------------------------------------------
    |
    | This is the notification class that will be sent to users when they receive
    | a confirmation code.
    |
    */
    'notification' => \BeyondCode\EmailConfirmation\Notifications\ConfirmEmail::class,

];
