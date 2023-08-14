<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

use CHYP\Partner\Echooss\Voucher\Type\Request\Request as DefaultRequest;

class QueryRedeemBatch extends DefaultRequest
{
    use StoreParamsTrait;

    /**
     * Reddem batch token.
     *
     * @var string
     */
    public string $batchToken = '';
}
