<?php

namespace Adata\HealthChecker\Http\Controllers;

use Adata\HealthChecker\Checkers\AbstractChecker;
use Adata\HealthChecker\Helpers\StatusConnectionHelper;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use \Adata\HealthChecker\Tests\Feature\HealthControllerTest;

/**
 * @uses HealthControllerTest
 */
class HealthController
{
    /**
     * @var array $healthConfig
     */
    private $healthConfig;

    public function __construct()
    {
        $this->healthConfig = Config::get('health');
    }

    /**
     * Health check
     */
    public function index(): JsonResponse
    {
        $result          = ['app' => data_get($this->healthConfig, 'app_name')];
        $dataConnections = [];
        $services        = data_get($this->healthConfig, 'services');
        data_get($this->healthConfig, 'enable_time_check') ? $apiStartTime = microtime(true) : $apiStartTime = null;

        if (!empty($services)) {
            foreach ($services as $key => $service) {
                $dataConnections[$key] = AbstractChecker::run($this->healthConfig, $key);
            }
        }

        data_get($this->healthConfig, 'show_host') ? $result['host'] = data_get($_SERVER, 'HTTP_HOST') : null;
        data_get($this->healthConfig, 'show_hostname') ? $result['hostname'] = exec('hostname') : null;
        data_get($this->healthConfig, 'enable_server_time') ? $result['time'] = Carbon::now() : null;

        data_get($this->healthConfig, 'enable_time_check') ?
            $result['execution'] = round(
                microtime(true) - $apiStartTime,
                data_get($this->healthConfig, 'precision_time', 2)) : null;

        data_get($this->healthConfig, 'enable_color_status') ?
            $result['health'] = StatusConnectionHelper::getColorStatus($dataConnections) : null;

        if (!empty($dataConnections)) {
            $result['services'] = $dataConnections;
        }

        return response()->json($result);
    }
}
