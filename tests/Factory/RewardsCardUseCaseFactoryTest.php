<?php

namespace Tests\Factory;

use CHYP\Partner\Echooss\Voucher\Application\Factory\RewardsCardUseCaseFactory;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\AccumulatePointUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\DepletePointUseCase;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;
use CHYP\Partner\Echooss\Voucher\Type\Request\AccumulatePoint;
use CHYP\Partner\Echooss\Voucher\Type\Request\DepletePoint;
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
        $payload = new AccumulatePoint();
        $payload->phoneNumber = '0912345678';
        $payload->amount = 100;

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
        $payload = new DepletePoint();
        $payload->phoneNumber = '0912345678';
        $payload->point = 50;

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

        $this->factory->create('invalidAction', new AccumulatePoint());
    }

    /**
     * 測試無效 payload (非 RequestInterface) 應該拋出例外。
     */
    public function testInvalidPayloadThrowsException(): void
    {
        $this->expectException(RequestTypeException::class);
        $this->expectExceptionMessage('RewardsCard request payload must implement RequestInterface');

        $this->factory->create('accumulatePoint', ['not' => 'a dto']);
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