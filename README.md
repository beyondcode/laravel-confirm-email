# Laravel Email Confirmation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/beyondcode/laravel-confirm-email.svg?style=flat-square)](https://packagist.org/packages/beyondcode/laravel-confirm-email)
[![Build Status](https://img.shields.io/travis/beyondcode/laravel-confirm-email/master.svg?style=flat-square)](https://travis-ci.org/beyondcode/laravel-confirm-email)
[![Quality Score](https://img.shields.io/scrutinizer/g/beyondcode/laravel-confirm-email.svg?style=flat-square)](https://scrutinizer-ci.com/g/beyondcode/laravel-confirm-email)
[![Total Downloads](https://img.shields.io/packagist/dt/beyondcode/laravel-confirm-email.svg?style=flat-square)](https://packagist.org/packages/beyondcode/laravel-confirm-email)

## Requirememnts
- Version 2.x requires PHP 7.1.3 and Laravel 5.6+
- Version 1.x requires PHP 7.1 and Laravel 5.5+
 
## Installation

You can install the package via composer:

```bash
composer require beyondcode/laravel-confirm-email
```

## Usage
> Note: this assumes you've run `php artisan make:auth` first

- This package adds a `confirmed_at` field to your `users` table. First, publish the migration, configuration file and routes using 

```bash
php artisan vendor:publish --provider=BeyondCode\\EmailConfirmation\\EmailConfirmationServiceProvider
```

- Then run the migrations:

```bash
php artisan migrate
```

- Replace the `AuthenticatesUsers`, `RegistersUsers` and the `SendsPasswordResetEmails` traits that
come with Laravel with the ones provided by this package.

These traits can be found in these three files:

- `App\Http\Controllers\Auth\LoginController`
- `App\Http\Controllers\Auth\RegisterController`
- `App\Http\Controllers\Auth\ForgotPasswordController`

## Routes
This package adds the routes needed for email verification:
- the `auth.resend_confirmation` route, which allows a user to request a new confirmation link (for instance, via a form on your site)
- the `auth.confirm` route, which is the confirmation link sent to the user's email. This package uses Laravel's [signed routes](https://laravel.com/docs/master/urls#signed-urls) to generate these links and tie them to users so there's no need to store them in a database. By default, these confirmation links will expire after 60 minutes. You can change this by setting the `timeout` value in the published `config/cnfirmation.php` file.

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
