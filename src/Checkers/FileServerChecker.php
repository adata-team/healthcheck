<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * FileServerChecker class
 *
 * type = fileServer
 */
class FileServerChecker implements CheckerInterface, HealthEntity
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
            $checkMounted = exec(sprintf('findmnt -T %s', $this->config['mounted_path']));

            if (empty($checkMounted) || !is_dir($this->config['mounted_path'])) {
                $status = self::STATUS_FAIL;
            }

            if (isset($this->config['dir']) && !is_dir($this->config['dir'])) {
                $status = self::STATUS_FAIL;
            }
        } catch (\Exception $e) {
            Log::warning('HEALTHCHECK: FileServerChecker have catch', ['error' => $e->getMessage()]);
            $status = self::STATUS_FAIL;
        }

        return $status;
    }
}
