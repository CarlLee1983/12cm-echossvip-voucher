<?php

namespace CHYP\Partner\Echooss\Voucher;

use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;

class Utils
{
    /**
     * Convert snake_case string to camelCase.
     *
     * @param string $input Raw string.
     *
     * @return string
     */
    public static function camelCase(string $input): string
    {
        $camelCase = ucwords(str_replace('_', ' ', $input));

        $camelCase = str_replace(' ', '', $camelCase);

        return lcfirst($camelCase);
    }

    /**
     * Convert camelCase string to snake_case.
     *
     * @param string $input Raw string.
     *
     * @return string
     */
    public static function snakeCase(string $input): string
    {
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_$1', $input));
    }

    /**
     * Validate Taiwan mobile phone format.
     *
     * @param string $value Phone number string.
     *
     * @return void
     */
    public static function validPhoneNumber(string $value): void
    {
        if (!preg_match('/^(\+886|0)[9]\d{8}$/', $value)) {
            throw new RequestTypeException('Invalid Taiwan mobile number. $value:' . $value);
        }
    }
}
