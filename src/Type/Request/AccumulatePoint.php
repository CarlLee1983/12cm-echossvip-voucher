<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

/**
 * Represents the request parameters for accumulating points for a member.
 * This class utilizes the PhoneNumberTrait for phone number validation and handling.
 */
class AccumulatePoint extends Request
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
     * e.g., $instance->phoneNumber = $instance->phoneNumber('0912345678');
     * @var string
     */
    public string $phoneNumber = '';

    /**
     * Required, Purchase Amount.
     *
     * @var int
     */
    public int $amount = 0;

    /**
     * Product Names and Quantities of Purchases. Multiple entries allowed.
     * Should be an array of AccumulatePointDetail objects. Optional, defaults to empty array.
     *
     * @var \CHYP\Partner\Echooss\Voucher\Type\Request\AccumulatePointDetail[]
     */
    public array $details = [];
}
