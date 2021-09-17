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
    /**
     * @inheritdoc
     */
    public static function check(array $config): string
    {
        $status    = self::STATUS_SUCCESSFUL;

        try {
            $timeout = self::DEFAULT_TIMEOUT;

            if (isset($config['timeout']) && !empty($config['timeout'])) {
                $timeout = $config['timeout'];
            }

            $client   = new Client();
            $request  = $client->get($config['url'], ['timeout' => $timeout]);
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