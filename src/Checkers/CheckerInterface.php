<?php

namespace Adata\HealthChecker\Checkers;

/**
 * CheckerInterface
 *
 * @package Adata\HealthChecker\Checkers
 */
interface CheckerInterface
{
    /**
     * Check service
     *
     * @return string
     */
    public function check(): string;
}