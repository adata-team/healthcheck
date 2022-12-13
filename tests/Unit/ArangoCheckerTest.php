<?php

namespace Adata\HealthChecker\Tests\Unit;

use Adata\HealthChecker\Checkers\ArangoChecker;
use Adata\HealthChecker\Entities\HealthEntity;
use Adata\HealthChecker\Tests\TestCase;
use GuzzleHttp\Psr7\Response;

class ArangoCheckerTest extends TestCase
{
    /**
     * @dataProvider getData
     */
    public function test(string $expectedHealthStatus, array $config, array $arangoResponse)
    {
        $response = new Response(
            $arangoResponse['status_code'],
            [],
            data_get($arangoResponse, 'body') ? json_encode($arangoResponse['body']) : null
        );

        $this->guzzleClientStub->method('post')->willReturn($response);

        $arango = new ArangoChecker($this->guzzleClientStub, $config);
        $status = $arango->check();

        $this->assertEquals($expectedHealthStatus, $status);
    }

    public function getData(): array
    {
        return [
            [
                'expected_health_status' => HealthEntity::STATUS_SUCCESSFUL,
                'config'                 => [
                    'type'     => 'arango',
                    'host'     => '127.0.0.1',
                    'port'     => 8529,
                    'https'    => false,
                    'database' => 'test',
                    'timeout'  => 5,
                    'query'    => 'return true',
                ],
                'arango_response'        => [
                    'status_code' => \Symfony\Component\HttpFoundation\Response::HTTP_CREATED,
                    'body'        => [
                        'code' => \Symfony\Component\HttpFoundation\Response::HTTP_CREATED,
                    ],
                ],
            ],
            [
                'expected_health_status' => HealthEntity::STATUS_FAIL,
                'config'                 => [
                    'type'     => 'arango',
                    'host'     => '127.0.0.1',
                    'port'     => 8529,
                    'https'    => false,
                    'database' => 'test',
                    'timeout'  => 5,
                    'query'    => 'return true',
                ],
                'arango_response'        => [
                    'status_code' => \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR,
                ],
            ],
            [
                'expected_health_status' => HealthEntity::STATUS_FAIL,
                'config'                 => [
                    'type'    => 'arango',
                    'port'    => 8529,
                    'https'   => false,
                    'timeout' => 5,
                    'query'   => 'return true',
                ],
                'arango_response'        => [
                    'status_code' => \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR,
                ],
            ],
        ];
    }
}
