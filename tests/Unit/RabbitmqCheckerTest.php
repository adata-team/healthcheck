<?php

namespace Tests\Unit;

use Adata\HealthChecker\Checkers\ArangoChecker;
use Adata\HealthChecker\Checkers\RabbitmqChecker;
use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;

class RabbitmqCheckerTest extends TestCase
{
    private $guzzleClientStub;
    private $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guzzleClientStub = $this->createStub(Client::class);
        $this->config = [
            'type'     => 'rabbitmq',
            'host'     => '127.0.0.1',
            'port'     => 5672,
            'user'     => 'user',
            'password' => 'password',
        ];
    }

    /**
     * @test success check
     */
    public function successCheckTest()
    {
        $this->guzzleClientStub->method('get')->willReturn(
            new Response(\Symfony\Component\HttpFoundation\Response::HTTP_CREATED, [], json_encode([
                [
                    'running' => true,
                ],
            ])),
        );

        $rabbit = new RabbitmqChecker($this->guzzleClientStub, $this->config);
        $status = $rabbit->check();

        $this->assertEquals(HealthEntity::STATUS_SUCCESSFUL, $status);
    }

    /**
     * @test fail check
     */
    public function failCheckTest()
    {
        $this->guzzleClientStub->method('get')->willReturn(
            new Response(\Symfony\Component\HttpFoundation\Response::HTTP_CREATED, [], json_encode([
                [
                    'running' => false,
                ],
            ])),
        );

        $rabbit = new RabbitmqChecker($this->guzzleClientStub, $this->config);
        $status = $rabbit->check();

        $this->assertEquals(HealthEntity::STATUS_FAIL, $status);
    }
}
