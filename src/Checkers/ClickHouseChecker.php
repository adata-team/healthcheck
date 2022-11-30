<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * ClickHouseChecker class
 *
 * type = clickhouse
 */
class ClickHouseChecker implements CheckerInterface, HealthEntity
{
    /**
     * Check service
     *
     * @param array $config
     *
     * @return array
     */
    public static function check(array $config): string
    {
        $status = self::STATUS_SUCCESSFUL;

        try {
            $protocol = self::DEFAULT_PROTOCOL;
            $timeout  = self::DEFAULT_TIMEOUT;
            $port     = self::DEFAULT_CLICKHOUSE_PORT;

            if (isset($config['port']) && !empty($config['port'])) {
                $port = $config['port'];
            }

            if (isset($config['timeout']) && !empty($config['timeout'])) {
                $timeout = $config['timeout'];
            }

            $url        = sprintf('%s://%s:%s/ping',
                $protocol,
                $config['host'],
                $port,
            );
            $client     = new Client();
            $response   = $client->get($url, ['timeout' => $timeout]);
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
