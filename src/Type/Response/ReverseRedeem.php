<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Response;

class ReverseRedeem extends Response
{
    /**
     * API request.
     *
     * @var boolean
     */
    public bool $success = false;

    /**
     * Response message.
     *
     * @var string
     */
    public string $message;
}
