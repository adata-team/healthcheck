<?php

namespace Adata\HealthChecker\Tests\Unit;

use Adata\HealthChecker\Checkers\NCANodeChecker;
use Adata\HealthChecker\Entities\HealthEntity;
use Adata\HealthChecker\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use \Symfony\Component\HttpFoundation\Response as StatusCode;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Psr7\Response;

/**
 * @covers \Adata\HealthChecker\Checkers\NCANodeChecker
 */
class NCANodeCheckerTest extends TestCase
{
	/**
	 * @covers       NCANodeChecker::check
	 * @dataProvider getData
	 * @throws GuzzleException
	 */
	public function test(string $expectedHealthStatus, array $config, array $httpResponse)
	{
		$this->guzzleClientStub->method('get')->willReturn(
			new Response($httpResponse['status_code']),
		);
		$http   = new NCANodeChecker($this->guzzleClientStub, $config);
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
					'url'     => 'http://10.10.22.169:31030',
					'timeout' => 2,
				],
				'http_response'          => [
					'status_code' => StatusCode::HTTP_OK,
				],
			],
			[
				'expected_health_status' => HealthEntity::STATUS_FAIL,
				'config'                 => [
					'type'    => 'http',
					'url'     => 'http://10.10.22.169:31030',
					'timeout' => 2,
				],
				'http_response'          => [
					'status_code' => StatusCode::HTTP_INTERNAL_SERVER_ERROR,
				],
			],
		];
	}
}
