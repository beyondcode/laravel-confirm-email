# Laravel Email Confirmation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/beyondcode/laravel-confirm-email.svg?style=flat-square)](https://packagist.org/packages/beyondcode/laravel-confirm-email)
[![Total Downloads](https://img.shields.io/packagist/dt/beyondcode/laravel-confirm-email.svg?style=flat-square)](https://packagist.org/packages/beyondcode/laravel-confirm-email)


## Installation

You can install the package via composer:

```bash
composer require beyondcode/laravel-confirm-email
```

## Usage

This package adds a `confirmed_at` and `confirmation_code` field to your users table.
Publish the migration and the configuration file using

```bash
php artisan vendor:publish --provider="BeyondCode\EmailConfirmation\EmailConfirmationServiceProvider"
```

And run the migrations:

```bash
php artisan migrate
```

### Configuring the login, register and forgot password controllers
In order to make use of the email verification, replace the `AuthenticatesUsers`, `RegistersUsers` and the `SendsPasswordResetEmails` traits that
come with Laravel, with the ones provided by this package.

These traits can be found in these three files:

- `App\Http\Controllers\Auth\LoginController`
- `App\Http\Controllers\Auth\RegisterController`
- `App\Http\Controllers\Auth\ForgotPasswordController`

### Add the confirmation and resend routes

Add the following two routes to your `routes/web.php` file:

```php
Route::name('auth.resend_confirmation')->get('/register/confirm/resend', 'Auth\RegisterController@resendConfirmation');
Route::name('auth.confirm')->get('/register/confirm/{confirmation_code}', 'Auth\RegisterController@confirm');
```

### Show confirmation messages

This packages adds some flash messages that contain error/information messages for your users.
To show them to your users, add this to your `login.blade.php`:

```blade
@if (session('confirmation'))
    <div class="alert alert-info" role="alert">
        {!! session('confirmation') !!}
    </div>
@endif
```
and this to both your `login.blade.php` and `email.blade.php`
```blade
@if ($errors->has('confirmation') > 0 )
    <div class="alert alert-danger" role="alert">
        {!! $errors->first('confirmation') !!}
    </div>
@endif
```

### Customization
This package comes with a language file, that allows you to modify the error / confirmation messages that your user
might see. In addition to that, you can change the notification class that will be used to send the confirmation code
completely, by changing it in the `config/confirmation.php` file.

### Change redirect routes
You can change all possible redirect routes by including these values either as properties in your registration controller, or as methods returning the route/URL string:

- `redirectConfirmationTo`
- `redirectAfterRegistrationTo`
- `redirectAfterResendConfirmationTo`

They all default to `route('login')`.

### The Confirmed Event
On successful email confirmation, this package dispatches a `Confirmed` event, in order for you to conveniently handle
any custom logic, such as sending a welcome email or automatically logging the user in.

Simply add the `Confirmed` event, and your listeners, to the `EventServiceProvider` in your application:

```php
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'BeyondCode\EmailConfirmation\Events\Confirmed' => [
            'App\Listeners\YourOnConfirmedListener'
        ]
    ];
```

For more information about registering events and listeners, please refer to the [Laravel docs](https://laravel.com/docs/events#registering-events-and-listeners).

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email marcel@beyondco.de instead of using the issue tracker.

## Credits

- [Marcel Pociot](https://github.com/mpociot)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
