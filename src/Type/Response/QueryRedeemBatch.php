<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Response;

class QueryRedeemBatch extends Response
{
    /**
     * API request.
     *
     * @var bool
     */
    public bool $success = false;

    /**
     * Uuid.
     *
     * @var string
     */
    public string $batchUuid;
}
