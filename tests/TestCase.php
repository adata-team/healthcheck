<?php

namespace Adata\HealthChecker\Tests;

use Adata\HealthChecker\HealthCheckerProvider;
use GuzzleHttp\Client;
use \PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected $guzzleClientStub;

    public function setUp(): void
    {
        parent::setUp();
        $this->guzzleClientStub = $this->createStub(Client::class);
    }

    protected function getPackageProviders($app)
    {
        return [
            HealthCheckerProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}