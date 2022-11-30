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
        'active_url'   => '/healthz',
    ],

    // |---------------------------------------------------------------------------------------------------------------|
    // |                                              SERVICE TYPES:                                                   |
    // |---------------------------------------------------------------------------------------------------------------|
    // | 1. rabbitmq                   - RabbitMQ queues (checked with Bunny\Client)                                   |
    // |      (string) host            - Server host                                                                   |
    // |      (int) port               - Connection port (Default: 15672)                                              |
    // |      (string) user            - User (Default: guest)                                                         |
    // |      (string) password        - Password (Default: guest)                                                     |
    // |      (int) timeout            - Connection timeout (Default: 3)                                               |
    // |---------------------------------------------------------------------------------------------------------------|
    // | 2. redis                      - Cache (checked with Predis\Client)                                            |
    // |      (string) host            - Server host                                                                   |
    // |      (int) port               - Connection port (Default: 6379)                                               |
    // |      (int) timeout            - Connection timeout (Default: 3)                                               |
    // |---------------------------------------------------------------------------------------------------------------|
    // | 3. healthCheck                - HealthCheck method (Used to check healthCheck)                                |
    // |      (string) url             - URL health check (URL where health check is available)                        |
    // |      (int) timeout            - Connection timeout (Default: 3)                                               |
    // |---------------------------------------------------------------------------------------------------------------|
    // | 4. database                   - Database (Checked using the DB facade)                                        |
    // |      (array|null) connections - Array of names of connections to the database (Default: set in .env)          |
    // |      (int) timeout            - Connection timeout (Default: 3)                                               |
    // |---------------------------------------------------------------------------------------------------------------|
    // | 5. http                       - Request 200 (Checked with Guzzle\Client)                                      |
    // |      (string) url             - URL where to check the 200 response status                                    |
    // |      (int) timeout            - Connection timeout (Default: 3)                                               |
    // |---------------------------------------------------------------------------------------------------------------|
    // | 6. elastic                    - ElasticSearch health check                                                    |
    // |      (array) hosts            - Elastic hosts to check (Format: "host:port")                                  |
    // |      (int) timeout            - Connection timeout (Default: 3)                                               |
    // |---------------------------------------------------------------------------------------------------------------|
    // | 7. fileServer                 - Checking file server availability                                             |
    // |      (string) mounted_path    - The path where the file server is mounted (to test mount)                     |
    // |      (string) dir             - Directory to check for existence                                              |
    // |---------------------------------------------------------------------------------------------------------------|
    // | 8. arango                     - ArangoDB Availability Check (Checks 201 response status)                      |
    // |      (string) host            - Server host arango                                                            |
    // |      (string|null) port       - Connection port (Default: 8529)                                               |
    // |      (bool) https             - Enable https protocol (Default: false)                                        |
    // |      (string) database        - Arango database name for the test                                             |
    // |      (int) timeout            - Connection timeout (Default: 3)                                               |
    // |---------------------------------------------------------------------------------------------------------------|
    // | 9. mail                       - Mail server check                                                             |
    // |      (string) host            - Mail server host                                                              |
    // |      (string) port            - Connection port (Default: 25)                                                 |
    // |      (int) timeout            - Connection timeout (Default: 3)                                               |
    // |---------------------------------------------------------------------------------------------------------------|
    'services'               => [
        'db'          => [
            'type'        => 'database',
            'connections' => ['pgsql'],
        ],
        'cache'       => [
            'type'    => 'redis',
            'host'    => '127.0.0.1',
            'port'    => 6379,
            'timeout' => 2,
        ],
        'fileServer'  => [
            'type'         => 'fileServer',
            'mounted_path' => '/mnt',
            'dir'          => '/mnt/some/folder',
        ],
        'queue'       => [
            'type'     => 'rabbitmq',
            'host'     => '127.0.0.1',
            'port'     => 15672,
            'user'     => 'test',
            'password' => 'secret',
            'timeout'  => 5,
        ],
        'search_api'  => [
            'type'  => 'elastic',
            'hosts' => ['127.0.0.1:9200'],
        ],
        'api'         => [
            'type'    => 'healthCheck',
            'url'     => 'https://example.com/health',
            'timeout' => 2,
        ],
        'another_api' => [
            'type'    => 'http',
            'url'     => 'https://example.com',
            'timeout' => 2,
        ],
        'arango'      => [
            'type'     => 'arango',
            'host'     => '127.0.0.1',
            'port'     => 8529,
            'https'    => false,
            'database' => 'test',
            'timeout'  => 5,
            'query'    => 'return true',
        ],
        'mail'        => [
            'type'    => 'mail',
            'host'    => '127.0.0.1',
            'port'    => 25,
            'timeout' => 3,
        ],
    ],
];