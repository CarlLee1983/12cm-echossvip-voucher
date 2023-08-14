<?php

use CHYP\Partner\Echooss\Voucher\Utils;
use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    public function testToCamelCase()
    {
        $originalString = "line_id_to_request";
        $camelCase = Utils::camelCase($originalString);

        $this->assertEquals($camelCase, 'lineIdToRequest');
    }

    public function testToSnakeCase()
    {
        $originalString = "lineIdToRequest";
        $snakeCase = Utils::snakeCase($originalString);

        $this->assertEquals($snakeCase, 'line_id_to_request');
    }

    public function testValidationPhoneName()
    {
        Utils::validPhoneNumber('0912123456');
        Utils::validPhoneNumber('+886912123456');

        $this->expectException(CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException::class);
        Utils::validPhoneNumber('123456789');
    }
}
