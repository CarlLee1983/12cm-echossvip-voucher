<?php

namespace CHYP\Partner\Echooss\Voucher;

use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;

class Utils
{
    /**
     * To camel case.
     *
     * @param string $input
     *
     * @return string
     */
    public static function camelCase(string $input): string
    {
        $camelCase = ucwords(str_replace("_", " ", $input));

        $camelCase = str_replace(" ", "", $camelCase);

        return lcfirst($camelCase);
    }

    /**
     * To sank case.
     *
     * @param string $input
     *
     * @return string
     */
    public static function snakeCase(string $input): string
    {
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_$1', $input));
    }

    /**
     * Validation phone number.
     *
     * @param string $value
     *
     * @return void
     */
    public static function validPhoneNumber(string $value)
    {
        if (!preg_match('/^(\+886|0)[9]\d{8}$/', $value)) {
            throw new RequestTypeException('Invalid Taiwan mobile number. $value:' . $value);
        }
    }
}
