<?php

namespace Tests\Application\UseCase\RewardsCard;

use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\AccumulatePointUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\DepletePointUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\RewardsCardUseCaseInterface;
use PHPUnit\Framework\TestCase;

/**
 * RewardsCard UseCase 單元測試。
 */
class RewardsCardUseCaseTest extends TestCase
{
    /**
     * 測試 AccumulatePointUseCase path。
     */
    public function testAccumulatePointUseCasePath(): void
    {
        $requests = [['phone_number' => '0912345678', 'amount' => 100]];
        $useCase = new AccumulatePointUseCase($requests);

        $this->assertEquals('/api/pos/mps-card-send-point', $useCase->path());
    }

    /**
     * 測試 AccumulatePointUseCase responseType。
     */
    public function testAccumulatePointUseCaseResponseType(): void
    {
        $requests = [];
        $useCase = new AccumulatePointUseCase($requests);

        $this->assertEquals('accumulatePoint', $useCase->responseType());
    }

    /**
     * 測試 AccumulatePointUseCase payload。
     */
    public function testAccumulatePointUseCasePayload(): void
    {
        $requests = [
            ['phone_number' => '0912345678', 'amount' => 100],
            ['phone_number' => '0923456789', 'amount' => 200],
        ];
        $useCase = new AccumulatePointUseCase($requests);

        $payload = $useCase->payload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('data', $payload);
        $this->assertEquals($requests, $payload['data']);
    }

    /**
     * 測試 AccumulatePointUseCase 實作 interface。
     */
    public function testAccumulatePointUseCaseImplementsInterface(): void
    {
        $useCase = new AccumulatePointUseCase([]);

        $this->assertInstanceOf(RewardsCardUseCaseInterface::class, $useCase);
    }

    /**
     * 測試 DepletePointUseCase path。
     */
    public function testDepletePointUseCasePath(): void
    {
        $requests = [['phone_number' => '0912345678', 'point' => 10]];
        $useCase = new DepletePointUseCase($requests);

        $this->assertEquals('/api/pos/mps-card-deduct-point', $useCase->path());
    }

    /**
     * 測試 DepletePointUseCase responseType。
     */
    public function testDepletePointUseCaseResponseType(): void
    {
        $requests = [];
        $useCase = new DepletePointUseCase($requests);

        $this->assertEquals('depletePoint', $useCase->responseType());
    }

    /**
     * 測試 DepletePointUseCase payload。
     */
    public function testDepletePointUseCasePayload(): void
    {
        $requests = [
            ['phone_number' => '0912345678', 'point' => 5],
        ];
        $useCase = new DepletePointUseCase($requests);

        $payload = $useCase->payload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('data', $payload);
        $this->assertEquals($requests, $payload['data']);
    }

    /**
     * 測試 DepletePointUseCase 實作 interface。
     */
    public function testDepletePointUseCaseImplementsInterface(): void
    {
        $useCase = new DepletePointUseCase([]);

        $this->assertInstanceOf(RewardsCardUseCaseInterface::class, $useCase);
    }

    /**
     * 測試空請求陣列。
     */
    public function testUseCaseWithEmptyRequests(): void
    {
        $useCase = new AccumulatePointUseCase([]);

        $payload = $useCase->payload();

        $this->assertEquals(['data' => []], $payload);
    }

    /**
     * 測試多筆請求。
     */
    public function testUseCaseWithMultipleRequests(): void
    {
        $requests = [
            ['phone_number' => '0912345678', 'amount' => 100],
            ['phone_number' => '0923456789', 'amount' => 200],
            ['phone_number' => '0934567890', 'amount' => 300],
        ];
        $useCase = new AccumulatePointUseCase($requests);

        $payload = $useCase->payload();

        $this->assertCount(3, $payload['data']);
    }

    /**
     * 使用 Data Provider 測試兩種 UseCase。
     *
     * @param string $useCaseClass UseCase 類別。
     * @param string $expectedPath 期望的 API 路徑。
     * @param string $expectedType 期望的回應類型。
     *
     * @dataProvider rewardsCardUseCaseProvider
     */
    public function testRewardsCardUseCases(
        string $useCaseClass,
        string $expectedPath,
        string $expectedType
    ): void {
        $useCase = new $useCaseClass([]);

        $this->assertInstanceOf(RewardsCardUseCaseInterface::class, $useCase);
        $this->assertEquals($expectedPath, $useCase->path());
        $this->assertEquals($expectedType, $useCase->responseType());
    }

    /**
     * 提供 RewardsCard UseCase 測試資料。
     *
     * @return array
     */
    public function rewardsCardUseCaseProvider(): array
    {
        return [
            [
                AccumulatePointUseCase::class,
                '/api/pos/mps-card-send-point',
                'accumulatePoint',
            ],
            [
                DepletePointUseCase::class,
                '/api/pos/mps-card-deduct-point',
                'depletePoint',
            ],
        ];
    }

    /**
     * 測試 payload 結構一致性。
     */
    public function testPayloadStructureConsistency(): void
    {
        $accumulateUseCase = new AccumulatePointUseCase([['test' => 1]]);
        $depleteUseCase = new DepletePointUseCase([['test' => 2]]);

        $accumulatePayload = $accumulateUseCase->payload();
        $depletePayload = $depleteUseCase->payload();

        // 確保兩者都有 'data' 鍵
        $this->assertArrayHasKey('data', $accumulatePayload);
        $this->assertArrayHasKey('data', $depletePayload);

        // 確保結構一致
        $this->assertEquals([['test' => 1]], $accumulatePayload['data']);
        $this->assertEquals([['test' => 2]], $depletePayload['data']);
    }
}

