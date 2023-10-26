<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Response;

class FreezeRedeemBatch extends Response
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

    /**
     * Freeze state code.
     *
     * The 0 is unfreeze.
     * The 1 is froze.
     *
     * @var int
     */
    public int $batchFreeze;
}
