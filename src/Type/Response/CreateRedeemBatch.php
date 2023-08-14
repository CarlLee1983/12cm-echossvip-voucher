<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Response;

class CreateRedeemBatch extends Response
{
    /**
     * API request.
     *
     * @var boolean
     */
    public bool $success = false;

    /**
     * Message.
     *
     * @var string
     */
    public string $message;

    /**
     * Token
     *
     * @var string
     */
    public string $batchToken;

    /**
     * Uuid.
     *
     * @var string
     */
    public string $batchUuid;
}
