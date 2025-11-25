<?php

namespace CHYP\Partner\Echooss\Voucher\Application\Gateway;

interface RewardsCardGatewayInterface
{
    /**
     * Send POST request to rewards card API.
     *
     * @param string $path    API path.
     * @param array  $payload Request payload.
     *
     * @return array Parsed response data.
     */
    public function post(string $path, array $payload): array;
}
