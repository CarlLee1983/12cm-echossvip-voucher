<?php

use CHYP\Partner\Echooss\Voucher\Core;
use CHYP\Partner\Echooss\Voucher\Exception\ResponseTypeException;
use CHYP\Partner\Echooss\Voucher\Type\Request\AccumulatePoint;
use CHYP\Partner\Echooss\Voucher\Type\Request\AccumulatePointDetail;
use CHYP\Partner\Echooss\Voucher\Type\Request\DepletePoint;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class RewardsCardTest extends TestCase
{
    /**
     * Call this template method before each test method is run.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->safeLoad();

        $this->core = (new Core(true))->setToken($_ENV['REWARDS_CARD_TOKEN']);
    }

    public function testRewardsCardAccumulatePointTest()
    {
        $param = new AccumulatePoint;
        $param->phoneNumber = '0912123456';
        $param->amount = 100;
        $param->details = [
            (new AccumulatePointDetail('test', 100, 1)),
        ];

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage('{"message":"phone number is not exist."}');

        $this->core->rewardsCard('accumulatePoint', [$param]);
    }

    public function testRewardsCardDepletePointTest()
    {
        $param = new DepletePoint;
        $param->phoneNumber = '0912123456';
        $param->point = 1;

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage('{"message":"phone number is not exist."}');

        $this->core->rewardsCard('depletePoint', [$param]);
    }
}
