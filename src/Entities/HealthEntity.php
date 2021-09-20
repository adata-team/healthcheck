<?php

namespace Adata\HealthChecker\Entities;

/**
 * Interface HealthEntity
 *
 * @package Adata\HealthChecker
 */
interface HealthEntity
{
    /**
     * Array colors that define the life of the project
     */
    public const COLORS_STATUS = [
            'alert' => 'red',
            'good'  => 'green',
            'bad'   => 'yellow',
        ];

    /**
     * Successful status connection
     */
    public const STATUS_SUCCESSFUL = 'ok';

    /**
     * Fail status connection
     */
    public const STATUS_FAIL = 'fail';

    /**
     * Redis send command to check
     */
    public const REDIS_SEND_COMMAND = "PING\r\n";

    /**
     * Redis check answer string
     */
    public const REDIS_CHECK_ANSWER = 'PONG';

    /**
     * Success connection message to database
     */
    public const DB_SUCCESS_CONN_MESSAGE = 'Connection OK; waiting to send.';

    /**
     * Default protocol for urls
     */
    public const DEFAULT_PROTOCOL = 'http';

    /**
     * Default port for connect to arangoDB
     */
    public const DEFAULT_ARANGO_PORT = 8529;

    /**
     * Default timeout in seconds
     */
    public const DEFAULT_TIMEOUT = 3;

    /**
     * Default arangoDB query to check
     */
    public const DEFAULT_ARANGO_QUERY = 'return true';

    /**
     * Default RabbitMQ port
     */
    public const DEFAULT_RABBITMQ_PORT = 15672;

    /**
     * Default Mail server port
     */
    public const DEFAULT_MAIL_PORT = 25;
}
