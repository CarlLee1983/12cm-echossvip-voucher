<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Response;

class VoucherList extends Response
{
    /**
     * Voucher list.
     *
     * @var array
     */
    public $data = [];

    /**
     * Object to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
