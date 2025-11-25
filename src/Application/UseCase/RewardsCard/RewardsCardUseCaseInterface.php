<?php

namespace CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard;

interface RewardsCardUseCaseInterface
{
    /**
        * Retrieve API path for this use case.
        *
        * @return string
        */
    public function path(): string;

    /**
        * Build payload array for the request.
        *
        * @return mixed
        */
    public function payload();

    /**
        * Resolve response type key.
        *
        * @return string
        */
    public function responseType(): string;
}
