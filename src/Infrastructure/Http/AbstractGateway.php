<?php

namespace CHYP\Partner\Echooss\Voucher\Infrastructure\Http;

use CHYP\Partner\Echooss\Voucher\Application\ApiContext;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;
use CHYP\Partner\Echooss\Voucher\Exception\ResponseTypeException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

abstract class AbstractGateway
{
    protected ClientInterface $client;
    protected ApiContext $context;

    /**
     * @param ClientInterface $client  HTTP client.
     * @param ApiContext      $context API context.
     */
    public function __construct(ClientInterface $client, ApiContext $context)
    {
        $this->client = $client;
        $this->context = $context;
    }

    /**
     * Send HTTP request and handle errors.
     *
     * @param string $method  HTTP verb.
     * @param string $uri     Target URI.
     * @param array  $payload JSON payload.
     *
     * @return array Parsed data payload.
     */
    protected function send(string $method, string $uri, array $payload): array
    {
        try {
            $response = $this->client->request(
                $method,
                $uri,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->context->token(),
                    ],
                    'json' => $payload,
                ]
            );

            $decoded = json_decode((string) $response->getBody(), true);

            return $decoded['data'] ?? [];
        } catch (RequestException $exception) {
            if ($exception->hasResponse()) {
                $response = $exception->getResponse();

                throw new ResponseTypeException(
                    (string) $response->getBody(),
                    $response->getStatusCode()
                );
            }

            throw new RequestTypeException('Request Error.');
        }
    }
}
