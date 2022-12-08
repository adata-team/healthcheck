<?php

namespace Tests\Checkers;

use Adata\HealthChecker\Checkers\FileServerChecker;
use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class FileServerCheckerTest extends TestCase
{
    private $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'type'         => 'fileServer',
            'mounted_path' => '/var/nas/',
            'dir'          => '/var/nas/',
        ];
    }

    /**
     * @test success check
     */
    public function successCheckTest()
    {
        $fileServer = new FileServerChecker(new Client(), $this->config);
        $status = $fileServer->check();
        
        $this->assertEquals(HealthEntity::STATUS_SUCCESSFUL, $status);
    }
}
