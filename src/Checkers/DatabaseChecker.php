<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use Adata\HealthChecker\Helpers\DatabaseHelper;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * DatabaseChecker class
 *
 * type = database
 */
class DatabaseChecker implements CheckerInterface, HealthEntity
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
            if (
                isset($this->config['connections']) &&
                is_array($this->config['connections']) &&
                !empty($this->config['connections'])) {
                foreach ($this->config['connections'] as $connection) {
                    if (is_string($connection)) {
                        if (!DatabaseHelper::checkConnection($connection)) {
                            $status = self::STATUS_FAIL;
                        }
                    }
                }
            } else {
                if (!DatabaseHelper::checkConnection(DB::getDefaultConnection())) {
                    $status = self::STATUS_FAIL;
                }
            }
        } catch (\Exception $e) {
            Log::warning('HEALTHCHECK: DatabaseChecker have catch', ['error' => $e->getMessage()]);
            $status = self::STATUS_FAIL;
        }

        return $status;
    }
}
