<?php

namespace CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher;

interface VoucherUseCaseInterface
{
    /**
        * Get API path for the current use case.
        *
        * @return string
        */
    public function path(): string;

    /**
        * Build request payload object or array.
        *
        * @return mixed
        */
    public function payload();

    /**
        * Resolve response type mapping key.
        *
        * @return string
        */
    public function responseType(): string;
}
