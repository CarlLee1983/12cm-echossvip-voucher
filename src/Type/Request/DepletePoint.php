<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

/**
 * Represents the request parameters for depleting points from a member's account.
 * This class utilizes the PhoneNumberTrait for phone number validation and handling.
 */
class DepletePoint extends Request
{
    use PhoneNumberTrait;

    /**
     * Data to be merged by parent Request::toArray() that doesn't have public properties.
     * Currently empty as all data points are defined as public properties.
     *
     * @var array
     */
    protected array $protectedData = [];

    /**
     * Required, Member's Cell Phone Number.
     * Use the phoneNumber() method from PhoneNumberTrait for validation when setting this.
     * @var string
     */
    public string $phoneNumber = '';

    /**
     * Required, Redemption Points (Amount of Cash Discount).
     *
     * @var int
     */
    public int $point = 0;
}
