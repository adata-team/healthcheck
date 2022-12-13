<?php

namespace Adata\HealthChecker\Tests\Feature;

use Adata\HealthChecker\Http\Controllers\HealthController;
use ReflectionException;

class HealthControllerTest extends \Orchestra\Testbench\TestCase
{
    public function test()
    {
        $exampleConfigPath = __DIR__ . '/../../Examples/config/health.php';
        $config            = require_once($exampleConfigPath);

        $controller   = $this->stubControllerProperty(HealthController::class, 'healthConfig', $config);
        $response     = $controller->index();
        $responseData = $response->getData(true);

        $this->assertArrayHasKey('app', $responseData);
        $this->assertArrayHasKey('host', $responseData);
        $this->assertArrayHasKey('hostname', $responseData);
        $this->assertArrayHasKey('time', $responseData);
        $this->assertArrayHasKey('execution', $responseData);
        $this->assertArrayHasKey('health', $responseData);
        $this->assertArrayHasKey('services', $responseData);
    }

    /**
     * @throws ReflectionException
     */
    protected function stubControllerProperty(string $controller, string $property, array $config)
    {
        $controller = new $controller();

        $reflection     = new \ReflectionClass($controller);
        $reflectionProp = $reflection->getProperty($property);
        $reflectionProp->setAccessible(true);
        $reflectionProp->setValue($controller, $config);

        return $controller;
    }
}
