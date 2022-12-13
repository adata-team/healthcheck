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
    /** @var array */
    private $config;

    public function __construct(Client $guzzleClient, array $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function check(): string
    {
        $status = self::STATUS_SUCCESSFUL;

        try {
            $timeout = self::DEFAULT_TIMEOUT;

            if (isset($this->config['timeout']) && !empty($this->config['timeout'])) {
                $timeout = $this->config['timeout'];
            }

            $socket = fsockopen(
                (string)$this->config['host'],
                (int)$this->config['port'],
                $errCode,
                $errStr,
                $this->config['timeout']
            );

            socket_set_timeout($socket, $timeout);

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
