<?php

namespace Tests\Application\Service;

use CHYP\Partner\Echooss\Voucher\Application\Assembler\RequestAssembler;
use CHYP\Partner\Echooss\Voucher\Application\Gateway\RewardsCardGatewayInterface;
use CHYP\Partner\Echooss\Voucher\Application\Hydrator\ResponseHydrator;
use CHYP\Partner\Echooss\Voucher\Application\Service\RewardsCardService;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\AccumulatePointUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\DepletePointUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\RewardsCardUseCaseInterface;
use CHYP\Partner\Echooss\Voucher\Type\Response\AccumulatePoint;
use CHYP\Partner\Echooss\Voucher\Type\Response\DepletePoint;
use CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface;
use PHPUnit\Framework\TestCase;

/**
 * RewardsCardService 單元測試。
 */
class RewardsCardServiceTest extends TestCase
{
    protected RewardsCardGatewayInterface $gateway;
    protected RequestAssembler $requestAssembler;
    protected ResponseHydrator $responseHydrator;
    protected RewardsCardService $service;

    protected function setUp(): void
    {
        $this->gateway = $this->createMock(RewardsCardGatewayInterface::class);
        $this->requestAssembler = $this->createMock(RequestAssembler::class);
        $this->responseHydrator = $this->createMock(ResponseHydrator::class);

        $this->service = new RewardsCardService(
            $this->gateway,
            $this->requestAssembler,
            $this->responseHydrator
        );
    }

    /**
     * 測試 handle AccumulatePoint 方法。
     */
    public function testHandleAccumulatePointUseCase(): void
    {
        $requests = [
            ['phone_number' => '0912345678', 'amount' => 100],
        ];
        $useCase = new AccumulatePointUseCase($requests);

        $rawResponse = ['message' => 'Success', 'point' => 10, 'amount' => 100];
        $expectedResponse = new AccumulatePoint();

        $this->requestAssembler->expects($this->once())
            ->method('toArray')
            ->with(['data' => $requests])
            ->willReturn(['data' => $requests]);

        $this->gateway->expects($this->once())
            ->method('post')
            ->with('/api/pos/mps-card-send-point', ['data' => $requests])
            ->willReturn($rawResponse);

        $this->responseHydrator->expects($this->once())
            ->method('hydrate')
            ->with('accumulatePoint', $rawResponse)
            ->willReturn($expectedResponse);

        $result = $this->service->handle($useCase);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame($expectedResponse, $result);
    }

    /**
     * 測試 handle DepletePoint 方法。
     */
    public function testHandleDepletePointUseCase(): void
    {
        $requests = [
            ['phone_number' => '0912345678', 'point' => 5],
        ];
        $useCase = new DepletePointUseCase($requests);

        $rawResponse = ['message' => 'Points depleted'];
        $expectedResponse = new DepletePoint();

        $this->requestAssembler->expects($this->once())
            ->method('toArray')
            ->willReturn(['data' => $requests]);

        $this->gateway->expects($this->once())
            ->method('post')
            ->with('/api/pos/mps-card-deduct-point', ['data' => $requests])
            ->willReturn($rawResponse);

        $this->responseHydrator->expects($this->once())
            ->method('hydrate')
            ->with('depletePoint', $rawResponse)
            ->willReturn($expectedResponse);

        $result = $this->service->handle($useCase);

        $this->assertInstanceOf(DepletePoint::class, $result);
    }

    /**
     * 測試 requestRaw 方法。
     */
    public function testRequestRawReturnsRawApiData(): void
    {
        $requests = [['phone_number' => '0912345678']];
        $useCase = new AccumulatePointUseCase($requests);

        $expectedRaw = ['message' => 'Success', 'point' => 100];

        $this->requestAssembler->expects($this->once())
            ->method('toArray')
            ->willReturn(['data' => $requests]);

        $this->gateway->expects($this->once())
            ->method('post')
            ->willReturn($expectedRaw);

        $result = $this->service->requestRaw($useCase);

        $this->assertEquals($expectedRaw, $result);
    }

    /**
     * 測試使用 Mock UseCase。
     */
    public function testHandleWithMockedUseCase(): void
    {
        $mockUseCase = $this->createMock(RewardsCardUseCaseInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);

        $mockUseCase->expects($this->once())
            ->method('payload')
            ->willReturn(['data' => [['test' => 'payload']]]);

        $mockUseCase->expects($this->once())
            ->method('path')
            ->willReturn('/test/rewards-path');

        $mockUseCase->expects($this->once())
            ->method('responseType')
            ->willReturn('testRewardsType');

        $this->requestAssembler->expects($this->once())
            ->method('toArray')
            ->willReturn(['data' => [['test' => 'payload']]]);

        $this->gateway->expects($this->once())
            ->method('post')
            ->with('/test/rewards-path', ['data' => [['test' => 'payload']]])
            ->willReturn(['success' => true]);

        $this->responseHydrator->expects($this->once())
            ->method('hydrate')
            ->with('testRewardsType', ['success' => true])
            ->willReturn($mockResponse);

        $result = $this->service->handle($mockUseCase);

        $this->assertSame($mockResponse, $result);
    }

    /**
     * 測試多筆請求的處理。
     */
    public function testHandleWithMultipleRequests(): void
    {
        $requests = [
            ['phone_number' => '0912345678', 'amount' => 100],
            ['phone_number' => '0923456789', 'amount' => 200],
        ];
        $useCase = new AccumulatePointUseCase($requests);
        $mockResponse = new AccumulatePoint();

        $this->requestAssembler->method('toArray')
            ->willReturn(['data' => $requests]);

        $this->gateway->expects($this->once())
            ->method('post')
            ->with('/api/pos/mps-card-send-point', ['data' => $requests]);

        $this->responseHydrator->method('hydrate')->willReturn($mockResponse);

        $this->service->handle($useCase);
    }

    /**
     * 測試空回應的處理。
     */
    public function testHandleWithEmptyResponse(): void
    {
        $useCase = new AccumulatePointUseCase([]);
        $mockResponse = new AccumulatePoint();

        $this->requestAssembler->method('toArray')->willReturn(['data' => []]);
        $this->gateway->method('post')->willReturn([]);
        $this->responseHydrator->method('hydrate')->willReturn($mockResponse);

        $result = $this->service->handle($useCase);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}

