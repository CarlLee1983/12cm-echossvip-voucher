<?php

namespace Tests\Application\Service;

use CHYP\Partner\Echooss\Voucher\Application\Assembler\RequestAssembler;
use CHYP\Partner\Echooss\Voucher\Application\Gateway\VoucherGatewayInterface;
use CHYP\Partner\Echooss\Voucher\Application\Hydrator\ResponseHydrator;
use CHYP\Partner\Echooss\Voucher\Application\Service\VoucherService;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\VoucherListUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\VoucherUseCaseInterface;
use CHYP\Partner\Echooss\Voucher\Type\Request\VoucherList;
use CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface;
use CHYP\Partner\Echooss\Voucher\Type\Response\VoucherList as VoucherListResponse;
use PHPUnit\Framework\TestCase;

/**
 * VoucherService 單元測試。
 */
class VoucherServiceTest extends TestCase
{
    protected VoucherGatewayInterface $gateway;
    protected RequestAssembler $requestAssembler;
    protected ResponseHydrator $responseHydrator;
    protected VoucherService $service;

    protected function setUp(): void
    {
        $this->gateway = $this->createMock(VoucherGatewayInterface::class);
        $this->requestAssembler = $this->createMock(RequestAssembler::class);
        $this->responseHydrator = $this->createMock(ResponseHydrator::class);

        $this->service = new VoucherService(
            $this->gateway,
            $this->requestAssembler,
            $this->responseHydrator
        );
    }

    /**
     * 測試 handle 方法回傳 ResponseInterface。
     */
    public function testHandleReturnsResponseInterface(): void
    {
        $request = new VoucherList();
        $request->lineId = 'test-line';
        $useCase = new VoucherListUseCase($request);

        $rawResponse = [['type' => 1, 'name' => 'Test Voucher']];
        $expectedResponse = new VoucherListResponse();

        $this->requestAssembler->expects($this->once())
            ->method('toArray')
            ->with($request)
            ->willReturn(['line_id' => 'test-line']);

        $this->gateway->expects($this->once())
            ->method('post')
            ->with('/pos_gateway/api/voucher-list', ['line_id' => 'test-line'])
            ->willReturn($rawResponse);

        $this->responseHydrator->expects($this->once())
            ->method('hydrate')
            ->with('voucherList', $rawResponse)
            ->willReturn($expectedResponse);

        $result = $this->service->handle($useCase);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame($expectedResponse, $result);
    }

    /**
     * 測試 requestRaw 方法回傳原始 API 資料。
     */
    public function testRequestRawReturnsRawApiData(): void
    {
        $request = new VoucherList();
        $request->phoneNumber = '0912345678';
        $useCase = new VoucherListUseCase($request);

        $expectedRaw = [
            ['type' => 1, 'voucher_hash_id' => 'abc123'],
            ['type' => 2, 'voucher_hash_id' => 'def456'],
        ];

        $this->requestAssembler->expects($this->once())
            ->method('toArray')
            ->willReturn(['phone_number' => '0912345678']);

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
        $mockUseCase = $this->createMock(VoucherUseCaseInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);

        $mockUseCase->expects($this->once())
            ->method('payload')
            ->willReturn(['test' => 'payload']);

        $mockUseCase->expects($this->once())
            ->method('path')
            ->willReturn('/test/path');

        $mockUseCase->expects($this->once())
            ->method('responseType')
            ->willReturn('testType');

        $this->requestAssembler->expects($this->once())
            ->method('toArray')
            ->with(['test' => 'payload'])
            ->willReturn(['test' => 'payload']);

        $this->gateway->expects($this->once())
            ->method('post')
            ->with('/test/path', ['test' => 'payload'])
            ->willReturn(['success' => true]);

        $this->responseHydrator->expects($this->once())
            ->method('hydrate')
            ->with('testType', ['success' => true])
            ->willReturn($mockResponse);

        $result = $this->service->handle($mockUseCase);

        $this->assertSame($mockResponse, $result);
    }

    /**
     * 測試 handle 呼叫 requestRaw 內部。
     */
    public function testHandleCallsRequestRawInternally(): void
    {
        $mockUseCase = $this->createMock(VoucherUseCaseInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);

        $mockUseCase->method('payload')->willReturn([]);
        $mockUseCase->method('path')->willReturn('/test');
        $mockUseCase->method('responseType')->willReturn('test');

        $this->requestAssembler->method('toArray')->willReturn([]);
        $this->gateway->expects($this->once())->method('post')->willReturn([]);
        $this->responseHydrator->method('hydrate')->willReturn($mockResponse);

        $this->service->handle($mockUseCase);
    }

    /**
     * 測試空回應的處理。
     */
    public function testHandleWithEmptyResponse(): void
    {
        $request = new VoucherList();
        $request->lineId = 'empty-test';
        $useCase = new VoucherListUseCase($request);
        $mockResponse = new VoucherListResponse();

        $this->requestAssembler->method('toArray')->willReturn([]);
        $this->gateway->method('post')->willReturn([]);
        $this->responseHydrator->method('hydrate')->willReturn($mockResponse);

        $result = $this->service->handle($useCase);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}

