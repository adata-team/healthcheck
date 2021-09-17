<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use Adata\HealthChecker\Helpers\DatabaseHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * DatabaseChecker class
 * 
 * type = database
 */
class DatabaseChecker implements CheckerInterface, HealthEntity
{
    /**
     * @inheritdoc
     */
    public static function check(array $config): string
    {
        $status = self::STATUS_SUCCESSFUL;

        try {
            if (
                isset($config['connections']) &&
                is_array($config['connections']) &&
                !empty($config['connections']))
            {
                foreach($config['connections'] as $connection) {
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