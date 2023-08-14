<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

trait StoreParamsTrait
{
    /**
     * Required, Brand Store ID.
     *
     * @var string
     */
    public string $storeOpenId = '';

    /**
     * Required, POS System Identification ID.
     *
     * @var string
     */
    public string $posMacUid = '';
}
