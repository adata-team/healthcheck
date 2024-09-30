<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use \Adata\HealthChecker\Tests\Unit\HttpCheckerTest;

class NCANodeChecker implements CheckerInterface, HealthEntity
{
	private $guzzleClient;

	/** @var array */
	private $config;
	public function __construct(Client $guzzleClient, array $config)
	{
		$this->guzzleClient = $guzzleClient;
		$this->config       = $config;
	}

	/**
	 * @inheritdoc
	 * @uses HttpCheckerTest::test()
	 */
	public function check(): string
	{
		$status = self::STATUS_SUCCESSFUL;

		try {
			$timeout = self::DEFAULT_TIMEOUT;

			if (isset($this->config['timeout']) && !empty($this->config['timeout'])) {
				$timeout = $this->config['timeout'];
			}

			$request = $this->guzzleClient->get($this->config['url'] . '/actuator/health', ['timeout' => $timeout]);

			if ($request->getStatusCode() !== Response::HTTP_OK) {
				$status = self::STATUS_FAIL;
			}
		} catch (\Exception $e) {
			Log::warning('HEALTHCHECK: HttpChecker have catch', ['error' => $e->getMessage()]);
			$status = self::STATUS_FAIL;
		} catch (GuzzleException $e) {
			Log::warning('HEALTHCHECK: HttpChecker have catch', ['error' => $e->getMessage()]);
			$status = self::STATUS_FAIL;
		}

		return $status;
	}
}