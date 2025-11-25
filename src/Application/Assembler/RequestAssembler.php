<?php

namespace CHYP\Partner\Echooss\Voucher\Application\Assembler;

use CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface;
use CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface;

class RequestAssembler
{
    /**
     * Recursively convert request/response objects into array payload.
     *
     * @param array|RequestInterface|ResponseInterface $payload Mixed request payload.
     *
     * @return array|mixed Converted payload.
     */
    public function toArray($payload)
    {
        if (is_array($payload)) {
            foreach ($payload as $index => $value) {
                $payload[$index] = $this->toArray($value);
            }

            return $payload;
        }

        if ($payload instanceof RequestInterface || $payload instanceof ResponseInterface) {
            return $this->toArray($payload->toArray());
        }

        return $payload;
    }
}
