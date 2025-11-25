<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

use CHYP\Partner\Echooss\Voucher\Utils;

trait PhoneNumberTrait
{
    /**
     * Validate and assign member phone number.
     *
     * @param string $phoneNumber Taiwan phone number string.
     *
     * @return string
     */
    public function phoneNumber(string $phoneNumber)
    {
        Utils::validPhoneNumber($phoneNumber);

        return $phoneNumber;
    }
}
