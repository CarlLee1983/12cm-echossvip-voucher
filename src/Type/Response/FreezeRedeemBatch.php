<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Response;

class FreezeRedeemBatch extends Response
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

    /**
     * Freeze state code.
     *
     * The 0 is unfreeze.
     * The 1 is froze.
     *
     * @var integer
     */
    public int $batchFreeze;
}
