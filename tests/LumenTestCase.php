<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\TestCase;
use Adata\HealthChecker\HealthCheckerProvider as HealthCheckerProvider;

class LumenTestCase extends TestCase
{
    public function createApplication()
    {
        $app = new Application(
            realpath(__DIR__)
        );

        if (empty($_SERVER['HTTP_HOST'])) {
            $_SERVER['HTTP_HOST'] = 'http://localhost';
        }

        $generator = $app->make('url');

        if (method_exists($generator, 'forceRootUrl')) {
            $generator->forceRootUrl(env('APP_URL', $_SERVER['HTTP_HOST']));
        } else {
            $uri = $app->make('config')->get('app.url', $_SERVER['HTTP_HOST']);

            $components = parse_url($uri);

            $server = $_SERVER;

            if (isset($components['path'])) {
                $server = array_merge($server, [
                    'SCRIPT_FILENAME' => $components['path'],
                    'SCRIPT_NAME' => $components['path'],
                ]);
            }

            $app->instance('request', \Illuminate\Http\Request::create(
                $uri, 'GET', [], [], [], $server
            ));
        }

        $app->withFacades();

        $app->configure('health');

        $app->singleton(
            ExceptionHandler::class,
            ExceptionsHandler::class,
        );

        $app->register(HealthCheckerProvider::class);

        return $app;
    }
}