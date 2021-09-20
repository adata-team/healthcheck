<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use Adata\HealthChecker\Entities\MailEntity;
use Adata\HealthChecker\Helpers\MailHelper;
use Illuminate\Support\Facades\Log;

/**
 * MailChecker class
 * 
 * type = mail
 */
class MailChecker implements CheckerInterface, HealthEntity, MailEntity
{
    /**
     * @inheritdoc
     */
    public static function check(array $config): string
    {
        $status = self::STATUS_SUCCESSFUL;

        try {
            $port    = self::DEFAULT_MAIL_PORT;
            $timeout = self::DEFAULT_TIMEOUT;

            if (isset($config['port']) && !empty($config['port'])) {
                $port = $config['port'];
            }

            if (isset($config['timeout']) && !empty($config['timeout'])) {
                $timeout = $config['timeout'];
            }

            $sock = fsockopen(
                $config['host'],
                $port,
                $errno,
                $errstr,
                $timeout
            );

            if(empty($sock)) {
                return self::STATUS_FAIL;
            }

            $res = fgets($sock, self::DEFAULT_BYTES);

            if (!is_string($res) || strlen($res) <= 0 || strpos($res, self::RESPONSE_SERVICE_READY) !== 0) {
                return self::STATUS_FAIL;
            }

            $data = [
                'auth'     => MailHelper::checkResponseCode($sock,
                    self::AUTH_MAIL_COMMAND,
                    self::RESPONSE_SERVER_CHALLENGE
                ),
                'username' => MailHelper::checkResponseCode($sock,
                    base64_encode($config['username']),
                    self::RESPONSE_SERVER_CHALLENGE
                ),
                'password' => MailHelper::checkResponseCode($sock,
                    base64_encode($config['password']),
                    self::RESPONSE_AUTH_SUCCESS
                ),
                'hello'    => MailHelper::checkResponseCode($sock,
                    self::HELO_MAIL_COMMAND,
                    self::RESPONSE_COMPLETED
                ),
                'bye'      => MailHelper::checkResponseCode($sock,
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