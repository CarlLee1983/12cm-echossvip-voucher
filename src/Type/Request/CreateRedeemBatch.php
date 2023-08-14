<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

use CHYP\Partner\Echooss\Voucher\Utils;

class CreateRedeemBatch extends Request
{
    use StoreParamsTrait;

    /**
     * Protected data.
     *
     * @var array
     */
    protected array $protectedData = [
        'phoneNumber' => '',
    ];

    /**
     * Member's Cell Phone Number. Validation phne number.
     *
     * @param string $phoneNumber
     *
     * @return string
     */
    public function phoneNumber(string $phoneNumber)
    {
        Utils::validPhoneNumber($phoneNumber);

        return $phoneNumber;
    }

    /**
     * Voucher batch list.
     *
     * @var array
     */
    public array $batchList = [];
}
