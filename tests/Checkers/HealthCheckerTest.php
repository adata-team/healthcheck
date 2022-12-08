<?php

namespace Tests\Checkers;

use Adata\HealthChecker\Checkers\ArangoChecker;
use Adata\HealthChecker\Checkers\HealthChecker;
use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;

class HealthCheckerTest extends TestCase
{
    private $guzzleClientStub;
    private $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guzzleClientStub = $this->createStub(Client::class);
        $this->config           = [
            'url'     => '127.0.0.1:8000',
            'timeout' => 2,
        ];
    }

    /**
     * @test success check
     */
    public function successCheckTest()
    {
        $this->guzzleClientStub->method('get')->willReturn(
            new Response(\Symfony\Component\HttpFoundation\Response::HTTP_CREATED, [], json_encode([
                'services' => [
                    ['result' => HealthEntity::STATUS_SUCCESSFUL],
                ],
            ])),
        );

        $health = new HealthChecker($this->guzzleClientStub, $this->config);
        $status = $health->check();
        
        $this->assertEquals(HealthEntity::STATUS_SUCCESSFUL, $status);
    }
}
