<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Response;

use CHYP\Partner\Echooss\Voucher\Utils;

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

    /**
     * Convert voucher rows into Voucher objects.
     *
     * @param array $rows Voucher rows from API.
     *
     * @return array<\CHYP\Partner\Echooss\Voucher\Type\Response\Voucher>
     */
    public function data(array $rows): array
    {
        $vouchers = [];

        foreach ($rows as $row) {
            $voucher = new Voucher();

            foreach ($row as $column => $value) {
                $property = Utils::camelCase((string) $column);
                $voucher->$property = $value;
            }

            $vouchers[] = $voucher;
        }

        return $vouchers;
    }
}
