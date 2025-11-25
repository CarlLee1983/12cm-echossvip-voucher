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
     * @param integer $redeemType     Redeem type code.
     * @param string  $redeemId       Voucher hash ID.
     * @param integer $redeemQuantity Quantity to redeem.
     */
    public function __construct(int $redeemType = 1, string $redeemId = '', int $redeemQuantity = 1)
    {
        $this->redeemType = $redeemType;
        $this->redeemId = $redeemId;
        $this->redeemQuantity = $redeemQuantity;
    }

    /**
     * Define redeem coupon type (1: coupon, 2: product voucher).
     *
     * @param integer $type Type code.
     *
     * @return integer Validated type code.
     */
    public function redeemType(int $type)
    {
        if ($type != 1 && $type != 2) {
            new RequestTypeException('Invalid redeem type.');
        }

        return $type;
    }
}
