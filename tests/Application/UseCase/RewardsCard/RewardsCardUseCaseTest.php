<?php

namespace Tests\Application\UseCase\RewardsCard;

use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\AccumulatePointUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\DepletePointUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\RewardsCardUseCaseInterface;
use CHYP\Partner\Echooss\Voucher\Type\Request\AccumulatePoint;
use CHYP\Partner\Echooss\Voucher\Type\Request\DepletePoint;
use PHPUnit\Framework\TestCase;

/**
 * RewardsCard UseCase 單元測試。
 */
class RewardsCardUseCaseTest extends TestCase
{
    /**
     * 測試 AccumulatePointUseCase。
     */
    public function testAccumulatePointUseCase(): void
    {
        $request = new AccumulatePoint();
        $request->phoneNumber = '0912345678';
        $request->amount = 100;
        
        $useCase = new AccumulatePointUseCase($request);

        $this->assertInstanceOf(RewardsCardUseCaseInterface::class, $useCase);
        $this->assertEquals('/api/pos/mps-card-send-point', $useCase->path());
        $this->assertEquals('accumulatePoint', $useCase->responseType());
        
        $payload = $useCase->payload();
        $this->assertIsArray($payload);
        $this->assertArrayHasKey('data', $payload);
        $this->assertIsArray($payload['data']);
        $this->assertCount(1, $payload['data']);
        $this->assertSame($request, $payload['data'][0]);
    }

    /**
     * 測試 DepletePointUseCase。
     */
    public function testDepletePointUseCase(): void
    {
        $request = new DepletePoint();
        $request->phoneNumber = '0912345678';
        $request->point = 10;
        
        $useCase = new DepletePointUseCase($request);

        $this->assertInstanceOf(RewardsCardUseCaseInterface::class, $useCase);
        $this->assertEquals('/api/pos/mps-card-deduct-point', $useCase->path());
        $this->assertEquals('depletePoint', $useCase->responseType());
        
        $payload = $useCase->payload();
        $this->assertIsArray($payload);
        $this->assertArrayHasKey('data', $payload);
        $this->assertCount(1, $payload['data']);
        $this->assertSame($request, $payload['data'][0]);
    }
}