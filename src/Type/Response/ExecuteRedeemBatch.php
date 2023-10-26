<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Response;

class ExecuteRedeemBatch extends Response
{
    /**
     * API request.
     *
     * @var bool
     */
    public bool $success = false;

    /**
     * Response message.
     *
     * @var string
     */
    public string $message;
}
