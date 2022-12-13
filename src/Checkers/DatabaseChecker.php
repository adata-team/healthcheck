<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use Adata\HealthChecker\Helpers\DatabaseHelper;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \Adata\HealthChecker\Tests\Unit\DatabaseCheckerTest;

/**
 * DatabaseChecker class
 *
 * type = database
 * @uses DatabaseCheckerTest
 */
class DatabaseChecker implements CheckerInterface, HealthEntity
{
    /** @var array */
    private $config;

    /** @var DatabaseHelper */
    private $dbHelper;

    public function __construct(Client $guzzleClient, array $config)
    {
        $this->config   = $config;
        $this->dbHelper = new DatabaseHelper();
    }

    /**
     * @inheritdoc
     * @uses DatabaseCheckerTest::test()
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
                    if (is_string($connection) && !$this->dbHelper->checkConnection($connection)) {
                        $status = self::STATUS_FAIL;
                    }
                }
            } elseif (!$this->dbHelper->checkConnection(DB::getDefaultConnection())) {
                $status = self::STATUS_FAIL;
            }
        } catch (\Exception $e) {
            Log::warning('HEALTHCHECK: DatabaseChecker have catch', ['error' => $e->getMessage()]);
            $status = self::STATUS_FAIL;
        }

        return $status;
    }
}
