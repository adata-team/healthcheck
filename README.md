# Health check library
[![PHP 7.3+ | 8.x](https://img.shields.io/badge/PHP-^7.3_|_^8-blue.svg)](https://packagist.org/packages/adata-team/healthchecker)
[![Composer v1|v2](https://img.shields.io/badge/Composer-^1.1_|_^2-success.svg)](https://packagist.org/packages/adata-team/healthchecker)
[![Latest version](https://img.shields.io/packagist/v/adata-team/healthchecker)](https://packagist.org/packages/adata-team/healthchecker)
[![Downloads](https://img.shields.io/packagist/dt/adata-team/healthchecker)](https://packagist.org/packages/adata-team/healthchecker)

## Quick start
1. Install via composer:
```shell
composer require adata-team/healthchecker
```
2. Connect service provider in Lumen `bootstrap/app.php`
```php
$app->register(Adata\HealthChecker\HealthCheckerProvider::class);
```
3. Make configuration file in `config/health.php` with content
```php
<?php

return [
    'router' => [
        'group_prefix' => 'health',
        'check_prefix' => 'check',
        'check_name'   => 'healthCheck',
        'url'          => '/health/check',
    ],
];
```
The above code is the minimum to run this plugin. 
* group_prefix - the name of the route group, for health
* check_prefix - health route prefix
* check_name   - route name
* url          - url where healthcheck will be available

------------------------
***P.S. A little later, there will be a description of the parameters.***
