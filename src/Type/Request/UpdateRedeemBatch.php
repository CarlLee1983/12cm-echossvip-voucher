<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

class UpdateRedeemBatch extends Request
{
    use StoreParamsTrait;

    /**
     * Redeem catch uuid.
     *
     * @var string
     */
    public string $batchUuid = '';

    /**
     * Voucher batch list.
     *
     * @var array
     */
    public array $batchList = [];
}
