<?php

namespace Adata\HealthChecker\Tests\Unit;

use Adata\HealthChecker\Checkers\ClickHouseChecker;
use Adata\HealthChecker\Entities\HealthEntity;
use Adata\HealthChecker\Tests\TestCase;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use \Symfony\Component\HttpFoundation\Response as StatusCode;

/**
 * @covers ClickHouseChecker
 */
class ClickHouseCheckerTest extends TestCase
{
    /**
     * @covers       ClickHouseChecker::check
     * @dataProvider getData
     * @throws GuzzleException
     */
    public function test(string $expectedHealthStatus, array $config, array $clickHouseResponse)
    {
        $response = new Response(
            $clickHouseResponse['status_code'],
        );

        $this->guzzleClientStub->method('get')->willReturn($response);

        $clickHouse = new ClickHouseChecker($this->guzzleClientStub, $config);
        $status     = $clickHouse->check();

        $this->assertEquals($expectedHealthStatus, $status);
    }

    public function getData(): array
    {
        return [
            [
                'expected_health_status' => HealthEntity::STATUS_SUCCESSFUL,
                'config'                 => [
                    'type'    => 'clickhouse',
                    'host'    => '127.0.0.1',
                    'port'    => 9000,
                    'timeout' => 2,
                ],
                'click_house_response'   => [
                    'status_code' => StatusCode::HTTP_OK,
                ],
            ],
        ];
    }
}
