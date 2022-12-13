<?php

namespace Adata\HealthChecker\Tests\Unit;

use Adata\HealthChecker\Checkers\DatabaseChecker;
use Adata\HealthChecker\Entities\HealthEntity;
use Adata\HealthChecker\Helpers\DatabaseHelper;
use GuzzleHttp\Client;
use Adata\HealthChecker\Tests\TestCase;
use ReflectionClass;

class DatabaseCheckerTest extends TestCase
{
    private $dbChecker;
    private $dbHelper;

    public function setUp(): void
    {
        parent::setUp();

        $this->dbHelperStub = $this->createStub(DatabaseHelper::class);
        $this->dbChecker    = new DatabaseChecker(new Client(), [
            'type'        => 'database',
            'connections' => ['pgsql'],
        ]);
        $reflection         = new ReflectionClass($this->dbChecker);
        $reflectionDbHelper = $reflection->getProperty('dbHelper');
        $reflectionDbHelper->setAccessible(true);
        $reflectionDbHelper->setValue($this->dbChecker, $this->dbHelperStub);

    }

    /**
     * @dataProvider getData
     */
    public function test(string $expectedHealthStatus, bool $dbHelperResponse)
    {
        $this->dbHelperStub->method('checkConnection')->willReturn($dbHelperResponse);
        $status = $this->dbChecker->check();
        $this->assertEquals($expectedHealthStatus, $status);
    }

    public function getData(): array
    {
        return [
            [
                'expected_health_status' => HealthEntity::STATUS_SUCCESSFUL,
                'db_helper_response'     => true,
            ],
            [
                'expected_health_status' => HealthEntity::STATUS_FAIL,
                'db_helper_response'     => false,
            ],
        ];
    }
}
