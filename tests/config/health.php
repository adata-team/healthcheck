<?php

return [
    // Application name to display in health check
    'app_name'               => env('APP_NAME', 'Project'),

    // Show host (URL of the current project)
    'show_host'              => true,

    // Rounding of time to n digits (Default: 2)
    'precision_time'         => 2,

    // Show server name
    'show_hostname'          => true,

    // Count how long it takes to complete the entire health check request
    'enable_time_check'      => true,

    // Enable display of health status [red, yellow, green]
    'enable_color_status'    => true,

    // Enable server time display (Carbon::now())
    'enable_server_time'     => true,

    // Valid elasticsearch cluster states (default only green status)
    // More details: https://www.elastic.co/guide/en/elasticsearch///reference/current/cluster-health.html#cluster-health-api-response-body
    'allowed_cluster_health' => ['yellow', 'green'],

    // Checkers class map.
    'class_map'              => [
        'rabbitmq'    => Adata\HealthChecker\Checkers\RabbitmqChecker::class,
        'redis'       => Adata\HealthChecker\Checkers\RedisChecker::class,
        'healthCheck' => Adata\HealthChecker\Checkers\HealthChecker::class,
        'database'    => Adata\HealthChecker\Checkers\DatabaseChecker::class,
        'http'        => Adata\HealthChecker\Checkers\HttpChecker::class,
        'elastic'     => Adata\HealthChecker\Checkers\ElasticsearchChecker::class,
        'fileServer'  => Adata\HealthChecker\Checkers\FileServerChecker::class,
        'arango'      => Adata\HealthChecker\Checkers\ArangoChecker::class,
        'mail'        => Adata\HealthChecker\Checkers\MailChecker::class,
    ],

    // Routing settings
    'router'                 => [
        'group_prefix' => 'health',
        'check_prefix' => 'check',
        'check_name'   => 'healthCheck',
        'url'          => '/health/check',
    ],
];