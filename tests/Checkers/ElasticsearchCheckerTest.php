<?php

namespace Tests\Checkers;

use Adata\HealthChecker\Checkers\ElasticsearchChecker;
use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;

class ElasticsearchCheckerTest extends TestCase
{
    private $guzzleClientStub;
    private $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guzzleClientStub = $this->createStub(Client::class);
        $this->config = [
            'type'  => 'elastic',
            'hosts' => ['10.10.1.6:9200', '10.10.1.7:9200']
        ];
    }

    /**
     * @test success check
     */
    public function successCheckTest()
    {
        $this->guzzleClientStub->method('get')->willReturn(
            new Response(\Symfony\Component\HttpFoundation\Response::HTTP_OK, [], json_encode([
                'status' => 'green',
            ])),
            new Response(\Symfony\Component\HttpFoundation\Response::HTTP_OK, [], json_encode([
                'status' => 'green',
            ])),
        );

        $elastic = new ElasticsearchChecker($this->guzzleClientStub, $this->config);
        $status = $elastic->check();

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

        $elastic = new ElasticsearchChecker($this->guzzleClientStub, $this->config);
        $status = $elastic->check();

        $this->assertEquals(HealthEntity::STATUS_FAIL, $status);
    }
}
