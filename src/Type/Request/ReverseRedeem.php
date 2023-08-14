<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

use CHYP\Partner\Echooss\Voucher\Utils;

class ReverseRedeem extends Request
{
    /**
     * Please provide either the request line ID or the phone number as parameters.
     *
     * @var string
     */
    public string $lindId;

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
     * Required, Redemption Coupon Type
     *
     * Type 1 represents coupon.
     * Type 2 represents Membership Welcome Voucher.
     * Type 3 represents Non-Member Product Voucher.
     *
     * @var integer
     */
    public int $type = 1;

    /**
     * Required, Voucher Hash ID.
     *
     * @var string
     */
    public string $voucherHashId = '';

    /**
     * Required, Reversal Redemption Quantity.
     *
     * @var integer
     */
    public int $deductCount = 1;
}
