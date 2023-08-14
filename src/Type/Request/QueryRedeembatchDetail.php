<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

class QueryRedeemBatchDetail extends Request
{
    use StoreParamsTrait;

    /**
     * Redeem catch uuid.
     *
     * @var string
     */
    public string $batchUuid = '';
}
