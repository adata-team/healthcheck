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
     * @param array $config
     *
     * @return string
     */
    public static function check(array $config): string;
}