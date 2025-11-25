<?php

namespace CHYP\Partner\Echooss\Voucher\Infrastructure\Http;

use CHYP\Partner\Echooss\Voucher\Application\ApiContext;
use CHYP\Partner\Echooss\Voucher\Application\Gateway\VoucherGatewayInterface;
use GuzzleHttp\ClientInterface;

class VoucherGateway extends AbstractGateway implements VoucherGatewayInterface
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
     * Send voucher POST request.
     *
     * @param string $path    API path.
     * @param array  $payload Request payload.
     *
     * @return array Parsed response data.
     */
    public function post(string $path, array $payload): array
    {
        $uri = $this->context->voucherBaseUri() . $path;

        return $this->send('POST', $uri, $payload);
    }
}
