<?php

namespace Adata\HealthChecker\Http\Controllers;

use Adata\HealthChecker\Checkers\AbstractChecker;
use Adata\HealthChecker\Helpers\StatusConnectionHelper;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

/**
 * HealthController class
 */
class HealthController
{
    /**
     * Health check
     */
    public function index(): JsonResponse
    {
        $result          = ['app' => config('health.app_name')];
        $dataConnections = [];
        $services        = config('health.services');
        config('health.enable_time_check') ? $apiStartTime = microtime(true) : $apiStartTime = null;

        if (!empty($services)) {
            foreach ($services as $key => $service) {
                $dataConnections[$key] = AbstractChecker::run($key);
            }
        }

        config('health.show_host')           ? $result['host']      = $_SERVER['HTTP_HOST'] : null;
        config('health.show_hostname')       ? $result['hostname']  = exec('hostname') : null;
        config('health.enable_server_time')  ? $result['time']      = Carbon::now() : null;
        config('health.enable_time_check')   ?
            $result['execution'] = round(
                microtime(true) - $apiStartTime,
                config('health.precision_time', 2)) : null;
        config('health.enable_color_status') ?
            $result['health'] = StatusConnectionHelper::getColorStatus($dataConnections) : null;

        if (!empty($dataConnections)) {
            $result['services'] = $dataConnections;
        }

        return response()->json($result);
    }
}
