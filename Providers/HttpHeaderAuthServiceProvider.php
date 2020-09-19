<?php

namespace Modules\HttpHeaderAuth\Providers;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use App\User;
use Modules\HttpHeaderAuth\Entities;

class HttpHeaderAuthServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->hooks();
    }

    /**
     * Module hooks.
     */
    public function hooks()
    {
        \Eventy::addAction('middleware.web.custom_handle', function ($request) {
            if (!$request->user() && isset($_SERVER['HTTP_X_AUTH_SUBJECT'])) {
                $httpuser = Entities\HttpUser::where('remote_id', $_SERVER['HTTP_X_AUTH_SUBJECT'])->first();
                if (!isset($httpuser)) {
                    $user = User::where('email', $_SERVER['HTTP_X_AUTH_EMAIL'])->first();
                    if (!isset($user)) {
                        $user = User::create([
                            "email" => $_SERVER['HTTP_X_AUTH_EMAIL'],
                            "first_name" => $_SERVER['HTTP_X_AUTH_USERNAME'],
                            "last_name" => ".",
                            "password" => str_random(64)
                        ]);
                        $user->save();
                    }

                    $httpuser = Entities\HttpUser::create([
                        "remote_id" => $_SERVER['HTTP_X_AUTH_SUBJECT'],
                        "user_id" => $user,
                    ]);
                } else {
                    $user = $httpuser->user;
                    $user->email = $_SERVER['HTTP_X_AUTH_EMAIL'];
                    $user->first_name = $_SERVER['HTTP_X_AUTH_USERNAME'];

                    $user->save();
                }

                if (isset($user)) {
                    \Auth::login($user);
                }
            }
        }, 20, 1);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerTranslations();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('httpheaderauth.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', 'httpheaderauth'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/httpheaderauth');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/httpheaderauth';
        }, \Config::get('view.paths')), [$sourcePath]), 'httpheaderauth');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadJsonTranslationsFrom(__DIR__ . '/../Resources/lang');
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (!app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
