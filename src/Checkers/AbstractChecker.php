<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;

/**
 * AbstractChecker class
 */
abstract class AbstractChecker
{
    /**
     * Run checker
     *
     * @param string     $service
     * @param array|null $configuration
     *
     * @return array
     */
    public function run($service): array
    {
        $config    = config(sprintf('health.services.%s', $service));
        $timeStart = microtime(true);
        $status    = HealthEntity::STATUS_FAIL;
        $classMap  = config('health.class_map');
        $type      = $config['type'];

        try {
            if (isset($classMap[$type]) && class_exists($classMap[$type])) {
                $service = new $classMap[$type];
                $status  = $service::check($config);
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