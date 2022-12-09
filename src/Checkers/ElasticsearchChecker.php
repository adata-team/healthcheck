<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

/**
 * ElasticsearchChecker class
 *
 * type = elastic
 */
class ElasticsearchChecker implements CheckerInterface, HealthEntity
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
     */
    public function check(): string
    {
        $status = self::STATUS_SUCCESSFUL;

        try {
            $timeout = self::DEFAULT_TIMEOUT;

            if (isset($this->config['timeout']) && !empty($this->config['timeout'])) {
                $timeout = $this->config['timeout'];
            }

            $normalStatuses = Config::get('health.allowed_cluster_health', ['green']);

            $apiId  = data_get($this->config, 'api_id');
            $apiKey = data_get($this->config, 'api_key');

            foreach ($this->config['hosts'] as $host) {
                $response     = $this->guzzleClient->get(sprintf('%s/_cluster/health', $host),
                    [
                        'timeout' => $timeout,
                        'headers' => [
                            'Accept'        => 'application/json',
                            'Content-Type'  => 'application/json',
                            'Authorization' => sprintf('ApiKey %s', base64_encode(sprintf('%s:%s', $apiId, $apiKey)))
                        ],
                    ]);
                $statusCode   = $response->getStatusCode() === Response::HTTP_OK;
                $responseBody = json_decode($response->getBody()->getContents(), true);

                if (!$statusCode || !in_array($responseBody['status'], $normalStatuses)) {
                    $status = self::STATUS_FAIL;
                    break;
                }
            }
        } catch (\Exception $e) {
            Log::warning('HEALTHCHECK: ElasticsearchChecker have catch', ['error' => $e->getMessage()]);
            $status = self::STATUS_FAIL;
        }

        return $status;
    }
}
