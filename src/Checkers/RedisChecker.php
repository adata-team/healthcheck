<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * RedisChecker class
 * 
 * type = redis
 */
class RedisChecker implements CheckerInterface, HealthEntity
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
            $timeout = self::DEFAULT_TIMEOUT;
            
            if (isset($config['timeout']) && !empty($config['timeout'])) {
                $timeout = $config['timeout'];
            }
            
            $socket = fsockopen(
                (string)$config['host'],
                (int)$config['port'],
                $errCode,
                $errStr,
                $config['timeout']
            );

            socket_set_timeout($socket, $config['timeout']);

            if ($errCode !== 0 && !empty($errStr)) {
                return self::STATUS_FAIL;
            }

            $writeCommand = fwrite($socket, self::REDIS_SEND_COMMAND);

            if ($writeCommand !== 6) {
                return self::STATUS_FAIL;
            }

            if (!(bool)stripos(fgets($socket), self::REDIS_CHECK_ANSWER)) {
                return self::STATUS_FAIL;
            }
        } catch (\Exception $e) {
            Log::warning('HEALTHCHECK: RedisChecker have catch', ['error' => $e->getMessage()]);
            $status = self::STATUS_FAIL;
        }

        return $status;
    }
}