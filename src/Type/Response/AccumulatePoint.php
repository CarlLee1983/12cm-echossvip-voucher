<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Response;

class AccumulatePoint extends Response
{
    /**
     * Response Message.
     *
     * @var string
     */
    public string $message = '';


    /**
     * Points Issued This Time
     *
     * @var integer
     */
    public int $point = 0;

    /**
     * Current Purchase Amount.
     *
     * @var integer|null
     */
    public ?int $amount;
}
