<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;

class Redeem extends Request
{
    /**
     * Required, Voucher Hash ID.
     *
     * @var string
     */
    public string $redeemId;

    /**
     * Required, Redeemable Quantity.
     *
     * @var int
     */
    public int $redeemQuantity;

    /**
     * __construct.
     *
     * @param int    $redeemId
     * @param string $redeemId
     * @param int    $redeemQuantity
     */
    public function __construct(int $redeemType = 1, string $redeemId = '', int $redeemQuantity = 1)
    {
        $this->redeemType = $redeemType;
        $this->redeemId = $redeemId;
        $this->redeemQuantity = $redeemQuantity;
    }

    /**
     * Required, Redemption Coupon Type.
     *
     * 1 represents member discount coupon.
     * 2 represents member product voucher.
     *
     * @var int
     */
    public function redeemType(int $type)
    {
        if ($type != 1 && $type != 2) {
            new RequestTypeException('Invalid redeem type.');
        }

        return $type;
    }
}
