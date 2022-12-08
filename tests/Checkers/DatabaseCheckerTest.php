<?php

namespace Tests\Checkers;

use Adata\HealthChecker\Checkers\DatabaseChecker;
use Adata\HealthChecker\Entities\HealthEntity;
use Adata\HealthChecker\Helpers\DatabaseHelper;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class DatabaseCheckerTest extends TestCase
{
    private $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'type'        => 'database',
            'connections' => ['pgsql'],
        ];
    }

    /**
     * @test success check
     */
    public function successCheckTest()
    {
        $dbHelperStub = $this->createStub(DatabaseHelper::class);
        $dbHelperStub->method('checkConnection')->willReturn(true);
        
        $dbChecker = new DatabaseChecker(new Client(), $this->config);
        $reflection = new \ReflectionClass($dbChecker);

        $reflectionDbHelper = $reflection->getProperty('dbHelper');
        $reflectionDbHelper->setAccessible(true);
        $reflectionDbHelper->setValue($dbChecker, $dbHelperStub);
        
        $status = $dbChecker->check();
        
        $this->assertEquals(HealthEntity::STATUS_SUCCESSFUL, $status);
    }

    /**
     * @test success check
     */
    public function failCheckTest()
    {
        $dbHelperStub = $this->createStub(DatabaseHelper::class);
        $dbHelperStub->method('checkConnection')->willReturn(false);

        $dbChecker = new DatabaseChecker(new Client(), $this->config);
        $reflection = new \ReflectionClass($dbChecker);

        $reflectionDbHelper = $reflection->getProperty('dbHelper');
        $reflectionDbHelper->setAccessible(true);
        $reflectionDbHelper->setValue($dbChecker, $dbHelperStub);

        $status = $dbChecker->check();

        $this->assertEquals(HealthEntity::STATUS_FAIL, $status);
    }
}
