<?php

namespace BeyondCode\EmailConfirmation\Tests;

use BeyondCode\EmailConfirmation\Tests\Controllers\ForgotPasswordController;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use BeyondCode\EmailConfirmation\Tests\Models\User;
use BeyondCode\EmailConfirmation\EmailConfirmationServiceProvider;
use BeyondCode\EmailConfirmation\Tests\Controllers\LoginController;
use BeyondCode\EmailConfirmation\Tests\Controllers\RegisterController;

class TestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();

        $this->setUpRoutes();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            EmailConfirmationServiceProvider::class,
        ];
    }
    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {

        $app['config']->set('app.debug', true);
        $app['config']->set('mail.driver', 'log');

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('app.key', '6rE9Nz59bGRbeMATftriyQjrpF7DcOQm');

        $app['config']->set('auth.providers.users.model', User::class);

        $kernel = $app->make(Kernel::class);
        $kernel->pushMiddleware(StartSession::class);
    }

    protected function setUpDatabase()
    {
        $this->createUserTable();

        include_once __DIR__.'/../database/migrations/add_confirmation_fields_to_users.php.stub';
        (new \AddConfirmationFieldsToUsers())->up();
    }

    protected function createUserTable()
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('password');
            $table->timestamps();
        });
    }

    protected function setUpRoutes()
    {
        Route::post('/register', RegisterController::class.'@register');
        Route::name('login')->post('/login', LoginController::class.'@login');
        Route::name('auth.resend_confirmation')->get('/register/resend_confirmation', RegisterController::class.'@resendConfirmation');
        Route::name('auth.confirm')->get('/register/confirm/{id}', RegisterController::class.'@confirm')->middleware('signed');
        Route::name('password.email')->post('/password/email', ForgotPasswordController::class.'@sendResetLinkEmail');
    }
}
