<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * RabbitmqChecker class
 * 
 * @type = rabbitmq
 */
class RabbitmqChecker implements CheckerInterface, HealthEntity
{
    /**
     * @inheritdoc
     */
    public static function check(array $config): string
    {
        $status = self::STATUS_SUCCESSFUL;

        try {
            $timeout = self::DEFAULT_TIMEOUT;
            $port    = self::DEFAULT_RABBITMQ_PORT;

            if (isset($config['timeout']) && !empty($config['timeout'])) {
                $timeout = $config['timeout'];
            }

            if (isset($config['port']) && !empty($config['port'])) {
                $port = $config['port'];
            }
            
            $url           = sprintf('%s:%s/api/nodes', $config['host'], $port);
            $authorization = ['auth' => [$config['user'], $config['password']], 'timeout' => $timeout];
            $client        = new Client();
            $request       = $client->get($url, $authorization);
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