<?php

namespace Adata\HealthChecker\Tests\Unit;

use Adata\HealthChecker\Checkers\HttpChecker;
use Adata\HealthChecker\Entities\HealthEntity;
use Adata\HealthChecker\Tests\TestCase;
use GuzzleHttp\Psr7\Response;

class HttpCheckerTest extends TestCase
{
    /**
     * @dataProvider  getData
     */
    public function test(string $expectedHealthStatus, array $config, array $httpResponse)
    {
        $this->guzzleClientStub->method('get')->willReturn(
            new Response($httpResponse['status_code']),
        );
        $http   = new HttpChecker($this->guzzleClientStub, $config);
        $status = $http->check();

        $this->assertEquals($expectedHealthStatus, $status);
    }

    public function getData(): array
    {
        return [
            [
                'expected_health_status' => HealthEntity::STATUS_SUCCESSFUL,
                'config'                 => [
                    'type'    => 'http',
                    'url'     => 'https://auth.adtdev.kz',
                    'timeout' => 2,
                ],
                'http_response'          => [
                    'status_code' => \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                ],
            ],
            [
                'expected_health_status' => HealthEntity::STATUS_FAIL,
                'config'                 => [
                    'type'    => 'http',
                    'url'     => 'https://auth.adtdev.kz',
                    'timeout' => 2,
                ],
                'http_response'          => [
                    'status_code' => \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR,
                ],
            ],
        ];
    }
}
