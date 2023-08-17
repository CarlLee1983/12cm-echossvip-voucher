<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

use CHYP\Partner\Echooss\Voucher\Type\Request\Request as DefaultRequest;
use CHYP\Partner\Echooss\Voucher\Utils;

class VoucherList extends DefaultRequest
{
    use PhoneNumberTrait;

    /**
     * Please provide either the request line ID or the phone number as parameters.
     *
     * @var string
     */
    public string $lineId;
}
