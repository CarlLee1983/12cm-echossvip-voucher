<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

class AccumulatePoint extends Request
{
    use PhoneNumberTrait;

    /**
     * Protected data.
     *
     * @var array
     */
    protected array $protectedData = [
        'phoneNumber' => '',
    ];

    /**
     * Required, Purchase Amount.
     *
     * @var int
     */
    public int $amount = 0;

    /**
     * Product Names and Quantities of Purchases. Multiple entries allowed, optional.
     *
     * @var array
     */
    public array $details;
}
