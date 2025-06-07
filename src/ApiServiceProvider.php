<?php

namespace MetaverseSystems\AuthApi;

use Illuminate\Support\ServiceProvider;
use App;
use MetaverseSystems\AuthApi\Controllers\UserController;
use MetaverseSystems\AuthApi\Models\Role;
use MetaverseSystems\AuthApi\Models\Permission;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        config(['auth.providers.users.model' => \MetaverseSystems\AuthApi\Models\User::class]);

        \Laravel\Sanctum\Sanctum::ignoreMigrations();

        $pat = base_path()."/database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php";
        if(file_exists($pat)) unlink($pat);

        $this->loadMigrationsFrom(__DIR__.'/Migrations');

        if(!$this->app->routesAreCached())
        {
            require __DIR__.'/Routes.php';
        }

        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('metaverse-auth-api.php'),
        ], 'config');

        App::booted(function() {
            app('router')->get('/', function() { return response()->json([], 200); })->middleware('web');
            app('router')->get('/{any}', function() { return response()->json([], 200); })->middleware('web')->where('any', '^.*$');
            app('router')->get('/api/user', [UserController::class, "index"])->middleware("auth:api");

            $guards = config('auth.guards');
            $guards['api'] = [
                'driver' => 'sanctum',
                'provider' => 'users',
                'hash' => false
            ];
            config(['auth.guards'=>$guards]);

/*
            $permissions = config('permission.models');
            $permissions['permission'] = Permission::class;
            $permissions['permission'] = Role::class;
            config(['permission.models'=>$permissions]);
*/
        });
    }
}
