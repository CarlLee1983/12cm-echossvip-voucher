<?php

namespace CHYP\Partner\Echooss\Voucher\Application\Gateway;

interface VoucherGatewayInterface
{
    /**
     * Send POST request to voucher API.
     *
     * @param string $path    API path.
     * @param array  $payload Request payload.
     *
     * @return array Parsed response data.
     */
    public function post(string $path, array $payload): array;
}
