<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

class CreateRedeemBatch extends Request
{
    use StoreParamsTrait, PhoneNumberTrait;

    /**
     * Protected data.
     *
     * @var array
     */
    protected array $protectedData = [
        'phoneNumber' => '',
    ];

    /**
     * Voucher batch list.
     *
     * @var array
     */
    public array $batchList = [];
}
