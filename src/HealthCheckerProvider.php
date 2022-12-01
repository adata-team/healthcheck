<?php

namespace Adata\HealthChecker;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class HealthCheckerProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        Route::group(array_filter(['as' => config('health.router.group_prefix', 'health')]), function () {
            Route::get(config('health.router.url'), array_filter(
                [
                    'as'   => config('health.router.check_prefix', 'check'),
                    'uses' => 'Adata\HealthChecker\Http\Controllers\HealthController@index',
                    'name' => config('health.router.check_name', 'check_name')
                ]
            ));
        });

        Route::group(array_filter(['as' => config('health.active.group_prefix', 'health')]), function () {
            Route::get(config('health.active.url'),
                [
                    'as'   => config('health.active.active_prefix', 'z'),
                    'name' => config('health.active.active_name', 'healthZ'),
                    function () {
                        return response(null, Response::HTTP_OK);
                    }
                ]
            );
        });
    }
}