<?php

namespace Adata\HealthChecker\Entities;

/**
 * Interface MailEntity
 *
 * @package Adata\HealthChecker
 */
interface MailEntity
{
    /**
     * Default count bytes to read message from server
     */
    public const DEFAULT_BYTES = 515;

    /**
     * Command to start authentification
     */
    public const AUTH_MAIL_COMMAND = "AUTH LOGIN";

    /**
     * Test command to check server
     */
    public const HELO_MAIL_COMMAND = "HELO localhost";

    /**
     * Command to close connection with server
     */
    public const QUIT_MAIL_COMMAND = "QUIT";

    /**
     * Mail status "Service ready"
     */
    public const RESPONSE_SERVICE_READY = '220';

    /**
     * Mail status "server challenge"
     */
    public const RESPONSE_SERVER_CHALLENGE = '334';

    /**
     * Mail status "authentication succeeded"
     */
    public const RESPONSE_AUTH_SUCCESS = '235';

    /**
     * Mail status "requested mail action okay, completed"
     */
    public const RESPONSE_COMPLETED = '250';

    /**
     * Mail status "service closing transmission channel" or "Goodbye"
     */
    public const RESPONSE_GOODBYE = '221';
}
