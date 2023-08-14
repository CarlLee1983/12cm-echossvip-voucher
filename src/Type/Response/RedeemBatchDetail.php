<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Response;

class RedeemBatchDetail
{
    /**
     * Redeem type.
     *
     * Type 1 represents coupon.
     * Type 2 represents Membership Welcome Voucher.
     * Type 3 represents not Membership Welcome Voucher.
     *
     * @var integer
     */
    public int $redeemType;

    /**
     * Redeem quantity.
     *
     * @var integer
     */
    public int $redeemQuantity;

    /**
     * Voucher hash id.
     *
     * @var string
     */
    public string $voucherHashId;

    /**
     * Vocuher name.
     *
     * @var string
     */
    public string $name;

    /**
     * Term id.
     *
     * @var string|null
     */
    public ?string $termId;

    /**
     * Coupon type.
     *
     * @var integer|null
     */
    public ?int $couponType;

    /**
     * Coupon type name.
     *
     * @var string|null
     */
    public ?string $typeName;
}
