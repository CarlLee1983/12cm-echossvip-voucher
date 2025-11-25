<?php

namespace Tests\Factory;

use CHYP\Partner\Echooss\Voucher\Application\Factory\RewardsCardUseCaseFactory;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\AccumulatePointUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\DepletePointUseCase;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;
use PHPUnit\Framework\TestCase;

/**
 * RewardsCardUseCaseFactory 單元測試。
 */
class RewardsCardUseCaseFactoryTest extends TestCase
{
    protected RewardsCardUseCaseFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new RewardsCardUseCaseFactory();
    }

    /**
     * 測試建立 AccumulatePointUseCase。
     */
    public function testCreateAccumulatePointUseCase(): void
    {
        $payload = [
            'phone_number' => '0912345678',
            'points' => 100,
        ];

        $useCase = $this->factory->create('accumulatePoint', $payload);

        $this->assertInstanceOf(AccumulatePointUseCase::class, $useCase);
        $this->assertEquals('/api/pos/mps-card-send-point', $useCase->path());
        $this->assertEquals('accumulatePoint', $useCase->responseType());
    }

    /**
     * 測試建立 DepletePointUseCase。
     */
    public function testCreateDepletePointUseCase(): void
    {
        $payload = [
            'phone_number' => '0912345678',
            'points' => 50,
        ];

        $useCase = $this->factory->create('depletePoint', $payload);

        $this->assertInstanceOf(DepletePointUseCase::class, $useCase);
        $this->assertEquals('/api/pos/mps-card-deduct-point', $useCase->path());
        $this->assertEquals('depletePoint', $useCase->responseType());
    }

    /**
     * 測試無效的 action 應該拋出例外。
     */
    public function testInvalidActionThrowsException(): void
    {
        $this->expectException(RequestTypeException::class);
        $this->expectExceptionMessage('Request action "invalidAction" not exists');

        $this->factory->create('invalidAction', []);
    }

    /**
     * 測試非陣列 payload 應該拋出例外。
     */
    public function testNonArrayPayloadThrowsException(): void
    {
        $this->expectException(RequestTypeException::class);
        $this->expectExceptionMessage('RewardsCard request payload must be an array');

        $this->factory->create('accumulatePoint', 'invalid');
    }

    /**
     * 測試 supports 方法。
     */
    public function testSupports(): void
    {
        $this->assertTrue($this->factory->supports('accumulatePoint'));
        $this->assertTrue($this->factory->supports('depletePoint'));

        $this->assertFalse($this->factory->supports('invalidAction'));
        $this->assertFalse($this->factory->supports(''));
    }

    /**
     * 測試 supportedActions 方法。
     */
    public function testSupportedActions(): void
    {
        $actions = $this->factory->supportedActions();

        $this->assertContains('accumulatePoint', $actions);
        $this->assertContains('depletePoint', $actions);
        $this->assertCount(2, $actions);
    }
}
