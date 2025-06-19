<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Resources' => 'App\Policies\Policy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::ignoreRoutes();

        // Mengambil nilai dari env dengan default 15 jika tidak ada
        $tokenExpireMinutes = env('PASSPORT_TOKEN_EXPIRE_MINUTES', 15);

        Passport::tokensExpireIn(now()->addMinutes($tokenExpireMinutes));
        Passport::refreshTokensExpireIn(now()->addMinutes($tokenExpireMinutes));
        Passport::personalAccessTokensExpireIn(now()->addMinutes($tokenExpireMinutes));

        Auth::extend('CUSTOM_AUTH', function ($app, $name, array $config) {
            $provider = new EloquentUserProvider($app['hash'], config('auth.providers.users.model'));

            $guard = new CustomAuth('CUSTOM_AUTH', $provider, app()->make('session.store'));
            $guard->setCookieJar($this->app['cookie']);

            return $guard;
        });

        //
    }
}
