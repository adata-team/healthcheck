<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use Adata\HealthChecker\Entities\MailEntity;
use Adata\HealthChecker\Helpers\MailHelper;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * MailChecker class
 *
 * type = mail
 */
class MailChecker implements CheckerInterface, HealthEntity, MailEntity
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
            $port    = self::DEFAULT_MAIL_PORT;
            $timeout = self::DEFAULT_TIMEOUT;

            if (isset($this->config['port']) && !empty($this->config['port'])) {
                $port = $this->config['port'];
            }

            if (isset($this->config['timeout']) && !empty($this->config['timeout'])) {
                $timeout = $this->config['timeout'];
            }

            $sock = fsockopen(
                $this->config['host'],
                $port,
                $errno,
                $errstr,
                $timeout
            );

            if (empty($sock)) {
                return self::STATUS_FAIL;
            }

            $res = fgets($sock, self::DEFAULT_BYTES);

            if (!is_string($res) || strlen($res) <= 0 || strpos($res, self::RESPONSE_SERVICE_READY) !== 0) {
                return self::STATUS_FAIL;
            }

            $data = [
                'hello' => MailHelper::checkResponseCode($sock,
                    self::HELO_MAIL_COMMAND,
                    self::RESPONSE_COMPLETED
                ),
                'bye'   => MailHelper::checkResponseCode($sock,
                    self::QUIT_MAIL_COMMAND,
                    self::RESPONSE_GOODBYE
                ),
            ];

            if (in_array(false, $data, true)) {
                return self::STATUS_FAIL;
            }
        } catch (\Exception $e) {
            Log::warning('HEALTHCHECK: MailChecker have catch', ['error' => $e->getMessage()]);
            $status = self::STATUS_FAIL;
        }

        return $status;
    }
}
