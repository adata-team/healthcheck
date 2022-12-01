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
     * @param string $service
     *
     * @return array
     */
    public static function run(string $service): array
    {
        $config    = config(sprintf('health.services.%s', $service));
        $timeStart = microtime(true);
        $status    = HealthEntity::STATUS_FAIL;
        $classMap  = config('health.class_map');
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
            'time'   => round($timeStop - $timeStart, config('health.precision_time', 2)),
        ];
    }
}
