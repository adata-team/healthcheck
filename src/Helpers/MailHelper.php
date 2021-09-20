<?php

namespace Adata\HealthChecker\Helpers;

use Adata\HealthChecker\Entities\MailEntity;

/**
 * MailHelper class
 */
class MailHelper implements MailEntity
{
    /**
     * Check response code from mail server
     *
     * @param $socket
     * @param string $data
     * @param string $code
     *
     * @return bool
     */
    public static function checkResponseCode($socket, string $data, string $code): bool
    {
        $string = sprintf("%s\r\n", $data);

        if (fwrite($socket, $string) !== strlen($string)) {
            return false;
        }

        $output = fgets($socket, self::DEFAULT_BYTES);

        return is_string($output) && $output !== '' && strpos($output, $code) === 0;
    }
}