<?php

namespace BeyondCode\EmailConfirmation;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\ServiceProvider;
use BeyondCode\EmailConfirmation\Listeners\CreateConfirmationCode;

class EmailConfirmationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('confirmation.php'),
            ], 'config');

            if (! class_exists('AddConfirmationFieldsToUsers')) {
                $this->publishes([
                    __DIR__.'/../database/migrations/add_confirmation_fields_to_users.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_add_confirmation_fields_to_users.php'),
                ], 'migrations');
            }

            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/confirmation'),
            ]);

        }

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/', 'confirmation');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'confirmation');
    }
}
