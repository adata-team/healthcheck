<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

/**
 * ElasticsearchChecker class
 *
 * type = elastic
 */
class ElasticsearchChecker implements CheckerInterface, HealthEntity
{
    /**
     * @inheritdoc
     */
    public static function check(array $config): string
    {
        $status = self::STATUS_SUCCESSFUL;

        try {
            $timeout = self::DEFAULT_TIMEOUT;

            if (isset($config['timeout']) && !empty($config['timeout'])) {
                $timeout = $config['timeout'];
            }

            $normalStatuses = config('health.allowed_cluster_health', ['green']);
            $apiId          = data_get($config, 'api_id');
            $apiKey         = data_get($config, 'api_key');

            foreach ($config['hosts'] as $host) {
                $client = new Client([
                    'headers' => [
                        'Accept'        => 'application/json',
                        'Content-Type'  => 'application/json',
                        'Authorization' => sprintf('ApiKey %s', base64_encode(sprintf('%s:%s', $apiId, $apiKey)))
                    ]
                ]);

                $request    = $client->get(sprintf('%s/_cluster/health', $host),
                    ['timeout' => $timeout]);
                $statusCode = $request->getStatusCode() === Response::HTTP_OK;
                $response   = json_decode($request->getBody()->getContents(), true);

                if (!$statusCode || !in_array($response['status'], $normalStatuses)) {
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