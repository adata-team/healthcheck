<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use \Adata\HealthChecker\Tests\Unit\ArangoCheckerTest;

/**
 * ArangoChecker class
 *
 * type = arango
 * @uses ArangoCheckerTest
 */
class ArangoChecker implements CheckerInterface, HealthEntity
{
    /** @var Client */
    private $guzzleClient;

    /** @var array */
    private $config;

    public function __construct(Client $guzzleClient, array $config)
    {
        $this->guzzleClient = $guzzleClient;
        $this->config       = $config;
    }

    /**
     * @inheritdoc
     * @uses ArangoCheckerTest::test()
     */
    public function check(): string
    {
        $status = self::STATUS_SUCCESSFUL;

        try {
            $protocol = self::DEFAULT_PROTOCOL;
            $port     = self::DEFAULT_ARANGO_PORT;
            $timeout  = self::DEFAULT_TIMEOUT;
            $query    = self::DEFAULT_ARANGO_QUERY;

            if (isset($this->config['https']) && $this->config['https']) {
                $protocol = 'https';
            }

            if (isset($this->config['port']) && !empty($this->config['port'])) {
                $port = $this->config['port'];
            }

            if (isset($this->config['timeout']) && !empty($this->config['timeout'])) {
                $timeout = $this->config['timeout'];
            }

            if (isset($this->config['query']) && !empty($this->config['query'])) {
                $query = $this->config['query'];
            }

            $url          = sprintf('%s://%s:%s/_db/%s/_api/cursor',
                $protocol,
                $this->config['host'],
                $port,
                $this->config['database']
            );
            $response     = $this->guzzleClient->post($url, ['json' => ['query' => $query], 'timeout' => $timeout]);
            $responseBody = json_decode($response->getBody()->getContents(), true);
            $statusCode   = $response->getStatusCode();

            if (
                Response::HTTP_CREATED !== $statusCode ||
                (isset($responseBody['code']) && $responseBody['code'] !== $statusCode)
            ) {
                $status = self::STATUS_FAIL;
            }
        } catch (\Exception $e) {
            Log::warning('HEALTHCHECK: ArangoChecker have catch', ['error' => $e->getMessage()]);
            $status = self::STATUS_FAIL;
        }

        return $status;
    }
}
