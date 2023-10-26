<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

use CHYP\Partner\Echooss\Voucher\Type\ValueHolderTrait;
use CHYP\Partner\Echooss\Voucher\Utils;

abstract class Request implements RequestInterface
{
    use ValueHolderTrait;

    /**
     * __construct.
     */
    public function __construct()
    {
    }

    /**
     * Output params.
     *
     * @return array
     */
    public function toArray(): array
    {
        $map = function ($valus) {
            $data = [];

            foreach ($valus as $key => $value) {
                $data[Utils::snakeCase($key)] = $value;
            }

            return $data;
        };

        $declaredAssets = get_object_vars($this);

        unset($declaredAssets['protectedData']);

        return array_merge(
            $map($declaredAssets),
            $map($this->protectedData ?? [])
        );
    }
}
