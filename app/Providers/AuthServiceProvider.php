<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Auth\JwtGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Auth::extend('jwt', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider'] ?? null);

            if ($provider === null) {
                throw new InvalidArgumentException("Invalid UserProvider config specified.");
            }

            return new JwtGuard($provider, $app['request'], $config['input_key'] ?? 'token');
        });
    }
}
