<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

/**
 * HttpChecker class
 * 
 * type = http
 */
class HttpChecker implements CheckerInterface, HealthEntity
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

            $client   = new Client();
            $request  = $client->get($config['url'], ['timeout' => $timeout]);

            if ($request->getStatusCode() !== Response::HTTP_OK) {
                $status = self::STATUS_FAIL;
            }
        } catch (\Exception $e) {
            Log::warning('HEALTHCHECK: HttpChecker have catch', ['error' => $e->getMessage()]);
            $status = self::STATUS_FAIL;
        } catch (GuzzleException $e) {
            Log::warning('HEALTHCHECK: HttpChecker have catch', ['error' => $e->getMessage()]);
            $status = self::STATUS_FAIL;
        }

        return $status;
    }
}