<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use Illuminate\Support\Facades\Log;

/**
 * FileServerChecker class
 * 
 * type = fileServer
 */
class FileServerChecker implements CheckerInterface, HealthEntity
{
    /**
     * @inheritdoc
     */
    public static function check(array $config): string
    {
        $status = self::STATUS_SUCCESSFUL;

        try {
            $checkMounted = exec(sprintf('findmnt -T %s', $config['mounted_path']));

            if (empty($checkMounted) || !is_dir($config['mounted_path'])) {
                $status = self::STATUS_FAIL;
            }

            if (isset($config['dir']) && !is_dir($config['dir'])) {
                $status = self::STATUS_FAIL;
            }
        } catch (\Exception $e) {
            Log::warning('HEALTHCHECK: FileServerChecker have catch', ['error' => $e->getMessage()]);
            $status = self::STATUS_FAIL;
        }

        return $status;
    }
}