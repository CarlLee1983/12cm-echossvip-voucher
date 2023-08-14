<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Response;

class QueryRedeemBatch extends Response
{
    /**
     * API request.
     *
     * @var boolean
     */
    public bool $success = false;

    /**
     * Uuid.
     *
     * @var string
     */
    public string $batchUuid;
}
