<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use \Adata\HealthChecker\Tests\Unit\ClickHouseCheckerTest;

/**
 * ClickHouseChecker class
 *
 * type = clickhouse
 * @uses ClickHouseCheckerTest
 */
class ClickHouseChecker implements CheckerInterface, HealthEntity
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
     *
     * @return string
     * @throws GuzzleException
     */
    public function check(): string
    {
        $status = self::STATUS_SUCCESSFUL;

        try {
            $protocol = self::DEFAULT_PROTOCOL;
            $timeout  = self::DEFAULT_TIMEOUT;
            $port     = self::DEFAULT_CLICKHOUSE_PORT;

            if (isset($this->config['port']) && !empty($this->config['port'])) {
                $port = $this->config['port'];
            }

            if (isset($this->config['timeout']) && !empty($this->config['timeout'])) {
                $timeout = $this->config['timeout'];
            }

            $url        = sprintf('%s://%s:%s/ping',
                $protocol,
                $this->config['host'],
                $port,
            );
            $response   = $this->guzzleClient->get($url, ['timeout' => $timeout]);
            $statusCode = $response->getStatusCode();

            if ($statusCode !== Response::HTTP_OK) {
                $status = self::STATUS_FAIL;
            }
        } catch (\Exception $e) {
            Log::warning('HEALTHCHECK: ClickHouseChecker have catch', ['error' => $e->getMessage()]);
            $status = self::STATUS_FAIL;
        }

        return $status;
    }
}
