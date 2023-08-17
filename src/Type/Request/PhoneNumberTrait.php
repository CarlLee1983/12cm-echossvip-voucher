<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

use CHYP\Partner\Echooss\Voucher\Utils;

trait PhoneNumberTrait
{
    /**
     * Required, Member's Cell Phone Number. Validation phne number.
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
}
