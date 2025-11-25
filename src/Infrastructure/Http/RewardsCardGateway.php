<?php

namespace CHYP\Partner\Echooss\Voucher\Infrastructure\Http;

use CHYP\Partner\Echooss\Voucher\Application\ApiContext;
use CHYP\Partner\Echooss\Voucher\Application\Gateway\RewardsCardGatewayInterface;
use GuzzleHttp\ClientInterface;

class RewardsCardGateway extends AbstractGateway implements RewardsCardGatewayInterface
{
    /**
     * @param ClientInterface $client  HTTP client.
     * @param ApiContext      $context API context.
     */
    public function __construct(ClientInterface $client, ApiContext $context)
    {
        parent::__construct($client, $context);
    }

    /**
     * Send rewards-card POST request.
     *
     * @param string $path    API path.
     * @param array  $payload Request payload.
     *
     * @return array Parsed response data.
     */
    public function post(string $path, array $payload): array
    {
        $uri = $this->context->rewardsCardBaseUri() . $path;

        return $this->send('POST', $uri, $payload);
    }
}
