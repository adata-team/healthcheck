<?php

namespace Adata\HealthChecker\Checkers;

use Adata\HealthChecker\Entities\HealthEntity;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

/**
 * ArangoChecker class
 * 
 * type = arango
 */
class ArangoChecker implements CheckerInterface, HealthEntity
{
    /**
     * @inheritdoc
     */
    public static function check(array $config): string
    {
        $status = self::STATUS_SUCCESSFUL;

        try {
            $protocol   = self::DEFAULT_PROTOCOL;
            $port       = self::DEFAULT_ARANGO_PORT;
            $timeout    = self::DEFAULT_TIMEOUT;
            $query      = self::DEFAULT_ARANGO_QUERY;
            $statusCode = Response::HTTP_CREATED;

            if (isset($config['https']) && $config['https']) {
                $protocol = 'https';
            }

            if (isset($config['port']) && !empty($config['port'])) {
                $port = $config['port'];
            }

            if (isset($config['timeout']) && !empty($config['timeout'])) {
                $timeout = $config['timeout'];
            }

            if (isset($config['query']) && !empty($config['query'])) {
                $query = $config['query'];
            }
            
            $url = sprintf('%s://%s:%s/_db/%s/_api/cursor',
                           $protocol,
                           $config['host'],
                           $port,
                           $config['database']
            );
            $client     = new Client();
            $request    = $client->post($url, ['json' => ['query' => $query], 'timeout' => $timeout]);
            $response   = json_decode($request->getBody()->getContents(), true);
            $statusCode = $request->getStatusCode();

            if (
                $statusCode !== $statusCode ||
                (isset($response['code']) && $response['code'] !== $statusCode)
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