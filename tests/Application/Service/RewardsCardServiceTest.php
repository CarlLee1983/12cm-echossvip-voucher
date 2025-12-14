<?php

namespace Tests\Application\Service;

use CHYP\Partner\Echooss\Voucher\Application\Assembler\RequestAssembler;
use CHYP\Partner\Echooss\Voucher\Application\Gateway\RewardsCardGatewayInterface;
use CHYP\Partner\Echooss\Voucher\Application\Hydrator\ResponseHydrator;
use CHYP\Partner\Echooss\Voucher\Application\Service\RewardsCardService;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\AccumulatePointUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\DepletePointUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\RewardsCardUseCaseInterface;
use CHYP\Partner\Echooss\Voucher\Type\Request\AccumulatePoint;
use CHYP\Partner\Echooss\Voucher\Type\Request\DepletePoint as DepletePointRequest;
use CHYP\Partner\Echooss\Voucher\Type\Response\AccumulatePoint as AccumulatePointResponse;
use CHYP\Partner\Echooss\Voucher\Type\Response\DepletePoint as DepletePointResponse;
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
        $request = new AccumulatePoint();
        $request->phoneNumber = '0912345678';
        $request->amount = 100;
        $useCase = new AccumulatePointUseCase($request);

        $rawPayload = ['phone_number' => '0912345678', 'amount' => 100];
        $assembledPayload = ['data' => [$rawPayload]];
        $rawResponse = ['message' => 'Success', 'point' => 10, 'amount' => 100];
        $expectedResponse = new AccumulatePointResponse();

        $this->requestAssembler->expects($this->once())
            ->method('toArray')
            ->with(['data' => [$request]])
            ->willReturn($assembledPayload);

        $this->gateway->expects($this->once())
            ->method('post')
            ->with('/api/pos/mps-card-send-point', $assembledPayload)
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
        $request = new DepletePointRequest();
        $request->phoneNumber = '0912345678';
        $request->point = 5;
        $useCase = new DepletePointUseCase($request);

        $rawPayload = ['phone_number' => '0912345678', 'point' => 5];
        $assembledPayload = ['data' => [$rawPayload]];
        $rawResponse = ['message' => 'Points depleted'];
        $expectedResponse = new DepletePointResponse();

        $this->requestAssembler->expects($this->once())
            ->method('toArray')
            ->with(['data' => [$request]])
            ->willReturn($assembledPayload);

        $this->gateway->expects($this->once())
            ->method('post')
            ->with('/api/pos/mps-card-deduct-point', $assembledPayload)
            ->willReturn($rawResponse);

        $this->responseHydrator->expects($this->once())
            ->method('hydrate')
            ->with('depletePoint', $rawResponse)
            ->willReturn($expectedResponse);

        $result = $this->service->handle($useCase);

        $this->assertInstanceOf(DepletePointResponse::class, $result);
    }

    /**
     * 測試 requestRaw 方法。
     */
    public function testRequestRawReturnsRawApiData(): void
    {
        $request = new AccumulatePoint();
        $request->phoneNumber = '0912345678';
        $useCase = new AccumulatePointUseCase($request);

        $assembledPayload = ['data' => [['phone_number' => '0912345678']]];
        $expectedRaw = ['message' => 'Success', 'point' => 100];

        $this->requestAssembler->expects($this->once())
            ->method('toArray')
            ->with(['data' => [$request]])
            ->willReturn($assembledPayload);

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
        $mockRequest = new AccumulatePoint(); // Dummy

        $mockUseCase->expects($this->once())
            ->method('payload')
            ->willReturn(['data' => [$mockRequest]]);

        $mockUseCase->expects($this->once())
            ->method('path')
            ->willReturn('/test/rewards-path');

        $mockUseCase->expects($this->once())
            ->method('responseType')
            ->willReturn('testRewardsType');

        $this->requestAssembler->expects($this->once())
            ->method('toArray')
            ->with(['data' => [$mockRequest]])
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
     * 測試空回應的處理。
     */
    public function testHandleWithEmptyResponse(): void
    {
        $request = new AccumulatePoint();
        $useCase = new AccumulatePointUseCase($request);
        $mockResponse = new AccumulatePointResponse();

        $this->requestAssembler->method('toArray')->willReturn(['data' => []]);
        $this->gateway->method('post')->willReturn([]);
        $this->responseHydrator->method('hydrate')->willReturn($mockResponse);

        $result = $this->service->handle($useCase);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}