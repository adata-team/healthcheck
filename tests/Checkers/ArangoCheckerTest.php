<?php

namespace Tests\Checkers;

use Adata\HealthChecker\Checkers\ArangoChecker;
use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;

class ArangoCheckerTest extends TestCase
{
    private $guzzleClientStub;
    private $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guzzleClientStub = $this->createStub(Client::class);
        $this->config = [
            'type'     => 'arango',
            'host'     => env('ARANGO_DB_HOST'),
            'port'     => env('ARANGO_DB_PORT'),
            'https'    => false,
            'database' => 'avroradata',
            'timeout'  => 5,
            'query'    => 'return true',
        ];
    }

    /**
     * @test success check
     */
    public function successCheckTest()
    {
        $this->guzzleClientStub->method('post')->willReturn(
            new Response(\Symfony\Component\HttpFoundation\Response::HTTP_CREATED, [], json_encode([
                'code' => \Symfony\Component\HttpFoundation\Response::HTTP_CREATED,
            ])),
        );

        $arango = new ArangoChecker($this->guzzleClientStub, $this->config);
        $status = $arango->check();

        $this->assertEquals(HealthEntity::STATUS_SUCCESSFUL, $status);
    }

    /**
     * @test fail check
     */
    public function failCheckTest()
    {
        $this->guzzleClientStub->method('post')->willReturn(
            new Response(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR),
        );

        $arango = new ArangoChecker($this->guzzleClientStub, $this->config);
        $status = $arango->check();

        $this->assertEquals(HealthEntity::STATUS_FAIL, $status);
    }
}
