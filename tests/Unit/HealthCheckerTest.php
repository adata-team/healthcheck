<?php

namespace Adata\HealthChecker\Tests\Unit;

use Adata\HealthChecker\Checkers\HealthChecker;
use Adata\HealthChecker\Entities\HealthEntity;
use Adata\HealthChecker\Tests\TestCase;
use GuzzleHttp\Psr7\Response;
use \Symfony\Component\HttpFoundation\Response as StatusCode;

/**
 * @covers HealthChecker
 */
class HealthCheckerTest extends TestCase
{
    /**
     * @dataProvider  getData
     * @covers        HealthChecker::check
     */
    public function test(string $expectedHealthStatus, array $config, array $healthResponse)
    {
        $response = new Response(
            $healthResponse['status_code'],
            [],
            json_encode($healthResponse['body'])
        );

        $this->guzzleClientStub->method('get')->willReturn($response);

        $health = new HealthChecker($this->guzzleClientStub, $config);
        $status = $health->check();

        $this->assertEquals($expectedHealthStatus, $status);
    }

    public function getData(): array
    {
        return [
            [
                'expected_health_status' => HealthEntity::STATUS_SUCCESSFUL,
                'config'                 => [
                    'url'     => '127.0.0.1:8000',
                    'timeout' => 2,
                ],
                'health_response'        => [
                    'status_code' => StatusCode::HTTP_CREATED,
                    'body'        => [
                        'services' => [
                            [
                                'result' => HealthEntity::STATUS_SUCCESSFUL,
                            ]
                        ],
                    ],
                ],
            ],
        ];
    }
}
