<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;

/**
 * AbstractChecker class
 */
abstract class AbstractChecker
{
    /**
     * Run checker
     *
     * @param array $healthConfig
     * @param string $service
     *
     * @return array
     */
    public static function run(array $healthConfig, string $service): array
    {
        $config    = data_get($healthConfig['services'], $service);
        $timeStart = microtime(true);
        $status    = HealthEntity::STATUS_FAIL;
        $classMap  = data_get($healthConfig, 'class_map');
        $type      = $config['type'];

        try {
            if (isset($classMap[$type]) && class_exists($classMap[$type])) {
                $service = new $classMap[$type](new Client(), $config);
                $status  = $service->check();
            }
        } catch (\Exception $exception) {
            $status = HealthEntity::STATUS_FAIL;
        }

        $timeStop = microtime(true);

        return [
            'result' => $status,
            'time'   => round($timeStop - $timeStart, data_get($healthConfig, 'precision_time', 2)),
        ];
    }
}
