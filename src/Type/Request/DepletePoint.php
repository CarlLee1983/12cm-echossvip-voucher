<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

class DepletePoint extends Request
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
     * Required, Redemption Points (Amount of Cash Discount).
     *
     * @var int
     */
    public int $point = 0;
}
