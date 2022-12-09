<?php

namespace Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase as BaseTestCase;
abstract class CheckerTestCase extends BaseTestCase
{
    protected $guzzleClientStub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guzzleClientStub = $this->createStub(Client::class);
    }
}
