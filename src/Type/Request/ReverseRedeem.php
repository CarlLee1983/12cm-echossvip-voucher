<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

class ReverseRedeem extends Request
{
    use PhoneNumberTrait;

    /**
     * Please provide either the request line ID or the phone number as parameters.
     *
     * @var string
     */
    public string $lindId;

    /**
     * Required, Redemption Coupon Type.
     *
     * Type 1 represents coupon.
     * Type 2 represents Membership Welcome Voucher.
     * Type 3 represents Non-Member Product Voucher.
     *
     * @var int
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
     * @var int
     */
    public int $deductCount = 1;
}
