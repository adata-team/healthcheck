<?php

namespace Adata\HealthChecker\Helpers;

use Adata\HealthChecker\Entities\HealthEntity;

/**
 * Class StatusConnectionHelper
 *
 * @package Adata\HealthChecker
 */
class StatusConnectionHelper implements HealthEntity
{
    /**
     * Get status color - check error connection
     *
     * @param array|null $data
     *
     * @return string
     */
    public static function getColorStatus(?array $data): string
    {
        $result      = self::COLORS_STATUS['good'];
        $countErrors = 0;

        if (empty($data)) {
            return $result;
        }

        foreach ($data as $status) {
            if ($status['result'] !== self::STATUS_SUCCESSFUL) {
                $countErrors++;
                $result = $countErrors === count($data)
                    ? self::COLORS_STATUS['alert']
                    : self::COLORS_STATUS['bad'];
            }
        }

        return $result;
    }

    /**
     * Method for check successfully status of services
     *
     * @param array $services
     *
     * @return string
     */
    public static function checkServices(array $services): string
    {
        foreach ($services as $service) {
            if ($service['result'] === self::STATUS_FAIL) {
                return self::STATUS_FAIL;
            }
        }

        return self::STATUS_SUCCESSFUL;
    }
}
