<?php

namespace Adata\HealthChecker;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

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
                    'as' => config('health.router.check_prefix', 'check'),
                    'uses' => 'Adata\HealthChecker\Http\Controllers\HealthController@index',
                    'name' => config('health.router.check_name', 'check_name')
                ]
            ));
        });
    }
}