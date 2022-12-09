<?php

namespace Tests\Unit;

use Adata\HealthChecker\Checkers\HttpChecker;
use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use Illuminate\Support\Env;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;

class HttpCheckerTest extends TestCase
{
    private $guzzleClientStub;
    private $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guzzleClientStub = $this->createStub(Client::class);
        $this->config = [
            'type'    => 'http',
            'url'     => Env::get('MODULE_AUTH_API'),
            'timeout' => 2,
        ];
    }

    /**
     * @test success check
     */
    public function successCheckTest()
    {
        $this->guzzleClientStub->method('get')->willReturn(
            new Response(\Symfony\Component\HttpFoundation\Response::HTTP_OK),
        );

        $http = new HttpChecker($this->guzzleClientStub, $this->config);
        $status = $http->check();

        $this->assertEquals(HealthEntity::STATUS_SUCCESSFUL, $status);
    }

    /**
     * @test fail check
     */
    public function failCheckTest()
    {
        $this->guzzleClientStub->method('get')->willReturn(
            new Response(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR),
        );

        $http = new HttpChecker($this->guzzleClientStub, $this->config);
        $status = $http->check();

        $this->assertEquals(HealthEntity::STATUS_FAIL, $status);
    }
}
