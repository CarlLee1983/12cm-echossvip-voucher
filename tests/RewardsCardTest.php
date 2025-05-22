<?php

use CHYP\Partner\Echooss\Voucher\Core;
use CHYP\Partner\Echooss\Voucher\Exception\ResponseTypeException;
use CHYP\Partner\Echooss\Voucher\Type\Request\AccumulatePoint;
use CHYP\Partner\Echooss\Voucher\Type\Request\AccumulatePointDetail;
use CHYP\Partner\Echooss\Voucher\Type\Request\DepletePoint;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for RewardsCard functionality.
 *
 * These tests interact with the live API (sandbox) and require .env configuration
 * for REWARDS_CARD_TOKEN. They primarily test error responses for non-existent
 * phone numbers as setting up valid, testable member data is complex for an automated suite.
 */
class RewardsCardTest extends TestCase
{
    /**
     * The Core service instance configured for RewardsCard.
     *
     * @var \CHYP\Partner\Echooss\Voucher\Core
     */
    protected Core $core;

    /**
     * Sets up the test environment before each test.
     *
     * Initializes Dotenv to load environment variables and configures the Core service
     * for rewards card operations in sandbox mode with the appropriate token.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->safeLoad();

        $this->core = (new Core(true, 'rewardsCard'))
            ->setToken($_ENV['REWARDS_CARD_TOKEN']);
    }

    /**
     * Tests the 'accumulatePoint' action of the rewards card service.
     *
     * It expects a ResponseTypeException with a specific message indicating the
     * phone number does not exist, as the test uses a dummy phone number.
     */
    public function testRewardsCardAccumulatePointTest(): void
    {
        $param = new AccumulatePoint();
        $param->phoneNumber = '0912123456'; // Dummy phone number
        $param->amount = 100;
        $param->details = [
            new AccumulatePointDetail('test product', 100, 1),
        ];

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage('{"message":"phone number is not exist."}');

        $this->core->rewardsCard('accumulatePoint', [$param]);
    }

    /**
     * Tests the 'depletePoint' action of the rewards card service.
     *
     * It expects a ResponseTypeException with a specific message indicating the
     * phone number does not exist, as the test uses a dummy phone number.
     */
    public function testRewardsCardDepletePointTest(): void
    {
        $param = new DepletePoint();
        $param->phoneNumber = '0912123456'; // Dummy phone number
        $param->point = 1;

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage('{"message":"phone number is not exist."}');

        $this->core->rewardsCard('depletePoint', [$param]);
    }
}
