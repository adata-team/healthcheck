<?php

namespace Adata\HealthChecker\Tests\Unit;

use Adata\HealthChecker\Checkers\RabbitmqChecker;
use Adata\HealthChecker\Entities\HealthEntity;
use Adata\HealthChecker\Tests\TestCase;
use GuzzleHttp\Psr7\Response;
use \Symfony\Component\HttpFoundation\Response as StatusCode;

/**
 * @covers RabbitmqChecker
 */
class RabbitmqCheckerTest extends TestCase
{
    /**
     * @dataProvider  getData
     * @covers        RabbitmqChecker::check
     */
    public function test(string $expectedHealthStatus, array $config, array $rabbitResponse)
    {
        $this->guzzleClientStub->method('get')->willReturn(
            new Response(
                $rabbitResponse['status_code'],
                [],
                json_encode($rabbitResponse['body'])
            ),
        );

        $rabbit = new RabbitmqChecker($this->guzzleClientStub, $config);
        $status = $rabbit->check();

        $this->assertEquals($expectedHealthStatus, $status);
    }

    public function getData(): array
    {
        return [
            [
                'expected_health_status' => HealthEntity::STATUS_SUCCESSFUL,
                'config'                 => [
                    'type'     => 'rabbitmq',
                    'host'     => '127.0.0.1',
                    'port'     => 5672,
                    'user'     => 'user',
                    'password' => 'password',
                ],
                'rabbit_response'        => [
                    'status_code' => StatusCode::HTTP_CREATED,
                    'body'        => [
                        [
                            'running' => true,
                        ],
                    ],
                ],
            ],
            [
                'expected_health_status' => HealthEntity::STATUS_FAIL,
                'config'                 => [
                    'type'     => 'rabbitmq',
                    'host'     => '127.0.0.1',
                    'port'     => 5672,
                    'user'     => 'user',
                    'password' => 'password',
                ],
                'rabbit_response'        => [
                    'status_code' => StatusCode::HTTP_CREATED,
                    'body'        => [
                        [
                            'running' => false,
                        ],
                    ],
                ],
            ],
        ];
    }
}
