# Health check library
[![PHP](https://img.shields.io/packagist/php-v/adata-team/healthchecker)](https://packagist.org/packages/adata-team/healthchecker)
[![Latest version](https://img.shields.io/packagist/v/adata-team/healthchecker)](https://packagist.org/packages/adata-team/healthchecker)
[![Stars](https://img.shields.io/packagist/stars/adata-team/healthchecker)](https://packagist.org/packages/adata-team/healthchecker)
[![Downloads](https://img.shields.io/packagist/dt/adata-team/healthchecker)](https://packagist.org/packages/adata-team/healthchecker)
[![Contributors](https://img.shields.io/github/contributors-anon/adata-team/healthcheck)](https://github.com/adata-team/healthcheck)
[![Release](https://img.shields.io/github/v/release/adata-team/healthcheck)](https://github.com/adata-team/healthcheck)

## Quick start for Lumen application 

###1. Install via composer:
```shell
composer require adata-team/healthchecker
```
###2. Make configuration file in `config/health.php` with content
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
###3. Register service provider in Lumen `bootstrap/app.php`
```php
$app->register(Adata\HealthChecker\HealthCheckerProvider::class);
```
and register configuration file
```php
$app->configure('health');
```
------------------------
## Parameters in configuration file

| NAME                   | DESCRIPTION                                             |  REQUIRED  | TYPE   | DEFAULT       | EXAMPLE                                                                                                          |
|------------------------|---------------------------------------------------------|------------|--------|---------------|------------------------------------------------------------------------------------------------------------------|
| app_name               | Application name to display in health check             |  *false*   | string | env[APP_NAME] | 'Project'                                                                                                        |
| show_host              | Show host (URL of the current project)                  |  *false*   | bool   | false         | true                                                                                                             |
| precision_time         | Rounding of time to n digits                            |  *false*   | int    | 2             | 4                                                                                                                |
| show_hostname          | Show server name                                        |  *false*   | bool   | false         | true                                                                                                             |
| enable_time_check      | Enable healthCheck runtime metering                     |  *false*   | bool   | false         | true                                                                                                             |
| enable_color_status    | Enable display of health status [red, yellow, green]    |  *false*   | bool   | false         | true                                                                                                             |
| enable_server_time     | Enable server time display (Carbon::now())              |  *false*   | bool   | false         | true                                                                                                             |
| allowed_cluster_health | Valid elasticsearch cluster states [red, yellow, green] |  *false*   | array  | [green]       | [yellow, green]                                                                                                  |
| class_map              | Checkers class map                                      |  *false*   | array  | []            | ['database' => Adata\HealthChecker\Checkers\DatabaseChecker::class]                                              |
| router                 | Routing settings                                        |  *true*    | array  | -             | ['group_prefix' => 'health', 'check_prefix' => 'check', 'check_name' => 'healthCheck', 'url' => '/health/check'] |
| services               | Services to check                                       |  *false*   | array  | -             | ['db' => ['type' => 'database', 'connections' => ['pgsql']]                                                      |

------------------------
## Configuration services to check
### RabbitMQ configuration params
- type = rabbitmq

| NAME     | DESCRIPTION     |  REQUIRED  | TYPE   | DEFAULT |
|----------|-----------------|------------|--------|---------|
| host     | Server host     |   *true*   | string |    -    |
| port     | Connection port |   *false*  | int    |  15672  |
| user     | User            |   *true*   | string |    -    |
| password | Password        |   *true*   | string |    -    |
| timeout  | Timeout         |   *false*  | int    |    3    |

### Redis configuration params
- type = redis

| NAME     | DESCRIPTION        |  REQUIRED  | TYPE   | DEFAULT |
|----------|--------------------|------------|--------|---------|
| host     | Server host        |   *true*   | string |    -    |
| port     | Connection port    |   *false*  | int    |  6379   |
| user     | User               |   *true*   | string |    -    |
| timeout  | Connection timeout |   *false*  | int    |    3    |

### Database configuration params
- type = database

| NAME         | DESCRIPTION              |  REQUIRED  | TYPE   | DEFAULT             |
|--------------|--------------------------|------------|--------|---------------------|
| connections  | Array naming connections |   *false*  | array  | .env[DB_CONNECTION] |
| timeout      | Connection timeout       |   *false*  | int    |    3                |

### HealthCheck configuration params
- type = healthCheck

| NAME     | DESCRIPTION        |  REQUIRED  | TYPE   | DEFAULT |
|----------|--------------------|------------|--------|---------|
| url      | URL health check   |   *true*   | string |    -    |
| timeout  | Connection timeout |   *false*  | int    |    3    |

### HTTP configuration params
- type = http

| NAME     | DESCRIPTION                                  |  REQUIRED  | TYPE   | DEFAULT |
|----------|----------------------------------------------|------------|--------|---------|
| url      | URL where to check the 200 response status   |   *true*   | string |    -    |
| timeout  | Connection timeout                           |   *false*  | int    |    3    |

### ElasticSearch configuration params
- type = elastic

| NAME     | DESCRIPTION                                  |  REQUIRED  | TYPE   | DEFAULT |
|----------|----------------------------------------------|------------|--------|---------|
| hosts    | Elastic hosts to check (Format: "host:port") |   *true*   | array  |    -    |
| timeout  | Connection timeout                           |   *false*  | int    |    3    |

### FileServer configuration params
- type = fileServer

| NAME         | DESCRIPTION                                  |  REQUIRED  | TYPE   | DEFAULT |
|--------------|----------------------------------------------|------------|--------|---------|
| mounted_path | The path where the file server is mounted    |   *true*   | string |    -    |
| dir          | Directory to check for existence             |   *true*   | string |    -    |

### Arango configuration params
- type = arango

| NAME     | DESCRIPTION                       |  REQUIRED  | TYPE   | DEFAULT |
|----------|-----------------------------------|------------|--------|---------|
| host     | Server host arango                |   *true*   | string |    -    |
| port     | Connection port                   |   *false*  | int    |  8529   |
| https    | Enable https protocol             |   *false*  | bool   |  false  |
| database | Arango database name for the test |   *true*   | string |    -    |
| timeout  | Connection timeout                |   *false*  | int    |    3    |