<?php

namespace Tests\Functional;

use Carbon\Carbon;
use Tests\LumenTestCase;

/**
 * Class RoutesTest
 *
 * @group   functional
 * @covers  HealthController::index
 * @package Tests\Functional
 */
class RoutesTest extends LumenTestCase
{
    /**
     * @test Get health/check route
     *
     * @group  functional
     * @covers HealthController::index
     */
    public function canAccessHealthRoute(): void
    {
        $this->get(config('health.router.url'))
             ->seeJson([
                           'app'      => config('health.app_name'),
                           'hostname' => gethostname(),
                           'health'   => 'green',
                       ])
             ->assertResponseOk();
    }
}