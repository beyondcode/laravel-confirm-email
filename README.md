# Laravel Email Confirmation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/beyondcode/laravel-confirm-email.svg?style=flat-square)](https://packagist.org/packages/beyondcode/laravel-confirm-email)
[![Build Status](https://img.shields.io/travis/beyondcode/laravel-confirm-email/master.svg?style=flat-square)](https://travis-ci.org/beyondcode/laravel-confirm-email)
[![Quality Score](https://img.shields.io/scrutinizer/g/beyondcode/laravel-confirm-email.svg?style=flat-square)](https://scrutinizer-ci.com/g/beyondcode/laravel-confirm-email)
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
php artisan vendor:publish --provider=BeyondCode\\EmailConfirmation\\EmailConfirmationServiceProvider
```

And run the migrations:

```bash
php artisan migrate
```

### Configuring the login and register controllers
In order to make use of the email verification, replace the `AuthenticatesUsers` and `RegistersUsers` traits that
come with Laravel, with the ones provided by this package.

These traits can be found in these two files:

- `App\Http\Controllers\Auth\LoginController`
- `App\Http\Controllers\Auth\RegisterController`

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
        {{ session('confirmation') }}
    </div>
@endif
```

### Customization
This package comes with a language file, that allows you to modify the error / confirmation messages that your user
might see. In addition to that, you can change the notification class that will be used to send the confirmation code
completely, by changing it in the `config/confirmation.php` file.

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
