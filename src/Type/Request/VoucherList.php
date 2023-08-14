<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

use CHYP\Partner\Echooss\Voucher\Type\Request\Request as DefaultRequest;
use CHYP\Partner\Echooss\Voucher\Utils;

class VoucherList extends DefaultRequest
{
    /**
     * Please provide either the request line ID or the phone number as parameters.
     *
     * @var string
     */
    public string $lineId;

    /**
     * Member's Cell Phone Number. Validation phne number.
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
