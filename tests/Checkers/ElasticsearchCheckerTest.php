<?php

namespace Tests\Checkers;

use Adata\HealthChecker\Checkers\ElasticsearchChecker;
use Adata\HealthChecker\Entities\HealthEntity;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;

class ElasticsearchCheckerTest extends TestCase
{
    /**
     * @dataProvider getData
     */
    public function test(string $expectedHealthStatus, array $config, array $elasticResponses)
    {
        $responses = [];
        foreach ($elasticResponses as $response) {
            $responses[] = new Response(
                $response['status_code'],
                [],
                json_encode([
                    'status' => $response['health_status'],
                ])
            );
        }

        $this->guzzleClientStub->method('get')->willReturn(...$responses);

        $elastic = new ElasticsearchChecker($this->guzzleClientStub, $config);
        $status  = $elastic->check();

        $this->assertEquals($expectedHealthStatus, $status);
    }

    public function getData(): array
    {
        return [
            [
                'expected_health_status' => HealthEntity::STATUS_SUCCESSFUL,
                'config'                 => [
                    'type'  => 'elastic',
                    'hosts' => ['10.10.1.6:9200']
                ],
                'elastic_responses'      => [
                    [
                        'status_code'   => \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                        'health_status' => 'green',
                    ],
                ],
            ],
            [
                'expected_health_status' => HealthEntity::STATUS_SUCCESSFUL,
                'config'                 => [
                    'type'  => 'elastic',
                    'hosts' => ['10.10.1.6:9200', '10.10.1.7:9200']
                ],
                'elastic_responses'      => [
                    [
                        'status_code'   => \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                        'health_status' => 'green',
                    ],
                    [
                        'status_code'   => \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                        'health_status' => 'green',
                    ],
                ],
            ],
            [
                'expected_health_status' => HealthEntity::STATUS_FAIL,
                'config'                 => [
                    'type'  => 'elastic',
                    'hosts' => ['10.10.1.6:9200']
                ],
                'elastic_responses'      => [
                    [
                        'status_code'   => \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                        'health_status' => 'red',
                    ],
                ],
            ],
            [
                'expected_health_status' => HealthEntity::STATUS_FAIL,
                'config'                 => [
                    'type'  => 'elastic',
                    'hosts' => ['10.10.1.6:9200']
                ],
                'elastic_responses'      => [
                    [
                        'status_code'   => \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR,
                        'health_status' => 'red',
                    ],
                ],
            ],
        ];
    }
}
