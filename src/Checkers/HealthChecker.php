<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use Adata\HealthChecker\Helpers\StatusConnectionHelper;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * HealthChecker class
 *
 * type = healthCheck
 */
class HealthChecker implements CheckerInterface, HealthEntity
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

            $request  = $this->guzzleClient->get($this->config['url'], ['timeout' => $timeout]);
            $response = json_decode($request->getBody()->getContents(), true);

            if (isset($response['services'])) {
                $status = StatusConnectionHelper::checkServices($response['services']);
            }

        } catch (\Exception $e) {
            Log::warning('HEALTHCHECK: HealthChecker have catch', ['error' => $e->getMessage()]);
            $status = self::STATUS_FAIL;
        } catch (GuzzleException $e) {
            Log::warning('HEALTHCHECK: HealthChecker have guzzle catch', ['error' => $e->getMessage()]);
            $status = self::STATUS_FAIL;
        }

        return $status;
    }
}
