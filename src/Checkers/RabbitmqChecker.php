<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use \Adata\HealthChecker\Tests\Unit\RabbitmqCheckerTest;

/**
 * RabbitmqChecker class
 *
 * @type = rabbitmq
 * @uses RabbitmqCheckerTest
 */
class RabbitmqChecker implements CheckerInterface, HealthEntity
{
    /** @var Client */
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
     * @uses RabbitmqCheckerTest::test()
     */
    public function check(): string
    {
        $status = self::STATUS_SUCCESSFUL;

        try {
            $timeout = self::DEFAULT_TIMEOUT;
            $port    = self::DEFAULT_RABBITMQ_PORT;

            if (isset($this->config['timeout']) && !empty($this->config['timeout'])) {
                $timeout = $this->config['timeout'];
            }

            if (isset($this->config['port']) && !empty($this->config['port'])) {
                $port = $this->config['port'];
            }

            $url           = sprintf('%s:%s/api/nodes', $this->config['host'], $port);
            $authorization = ['auth' => [$this->config['user'], $this->config['password']], 'timeout' => $timeout];
            $request       = $this->guzzleClient->get($url, $authorization);
            $response      = json_decode($request->getBody()->getContents(), true);

            if (!isset($response[0]['running']) || !$response[0]['running']) {
                $status = self::STATUS_FAIL;
            }
        } catch (\Exception $e) {
            Log::warning('HEALTHCHECK: RabbitmqChecker have catch', ['error' => $e->getMessage()]);
            $status = self::STATUS_FAIL;
        }

        return $status;
    }
}
