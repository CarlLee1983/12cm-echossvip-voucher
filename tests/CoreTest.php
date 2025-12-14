<?php

namespace Tests;

use CHYP\Partner\Echooss\Voucher\Application\ApiContext;
use CHYP\Partner\Echooss\Voucher\Application\Factory\RewardsCardUseCaseFactory;
use CHYP\Partner\Echooss\Voucher\Application\Factory\VoucherUseCaseFactory;
use CHYP\Partner\Echooss\Voucher\Application\Service\RewardsCardService;
use CHYP\Partner\Echooss\Voucher\Application\Service\VoucherService;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\VoucherListUseCase;
use CHYP\Partner\Echooss\Voucher\Core;
use CHYP\Partner\Echooss\Voucher\Type\Request\AccumulatePoint;
use CHYP\Partner\Echooss\Voucher\Type\Request\CreateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\Redeem;
use CHYP\Partner\Echooss\Voucher\Type\Request\VoucherList;
use CHYP\Partner\Echooss\Voucher\Type\Response\AccumulatePoint as AccumulatePointResponse;
use CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface;
use CHYP\Partner\Echooss\Voucher\Type\Response\VoucherList as VoucherListResponse;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;

/**
 * Core 類別單元測試。
 */
class CoreTest extends TestCase
{
    /**
     * 測試預設建構子使用 production 環境。
     */
    public function testDefaultConstructorUsesProduction(): void
    {
        $core = new Core();

        $this->assertFalse($core->useSandbox());
    }

    /**
     * 測試建構子設定 sandbox 模式。
     */
    public function testConstructorWithSandboxEnabled(): void
    {
        $core = new Core(true);

        $this->assertTrue($core->useSandbox());
    }

    /**
     * 測試 getToken 預設為空字串。
     */
    public function testGetTokenDefaultsToEmptyString(): void
    {
        $core = new Core();

        $this->assertEquals('', $core->getToken());
    }

    /**
     * 測試 setToken 設定 token 並回傳 fluent interface。
     */
    public function testSetTokenSetsTokenAndReturnsSelf(): void
    {
        $core = new Core();
        $token = 'test-bearer-token';

        $result = $core->setToken($token);

        $this->assertEquals($token, $core->getToken());
        $this->assertSame($core, $result);
    }

    /**
     * 測試 getVoucherUseCaseFactory 回傳工廠實例。
     */
    public function testGetVoucherUseCaseFactoryReturnsFactory(): void
    {
        $core = new Core();

        $factory = $core->getVoucherUseCaseFactory();

        $this->assertInstanceOf(VoucherUseCaseFactory::class, $factory);
    }

    /**
     * 測試 getRewardsCardUseCaseFactory 回傳工廠實例。
     */
    public function testGetRewardsCardUseCaseFactoryReturnsFactory(): void
    {
        $core = new Core();

        $factory = $core->getRewardsCardUseCaseFactory();

        $this->assertInstanceOf(RewardsCardUseCaseFactory::class, $factory);
    }

    /**
     * 測試建構子接受自訂 ApiContext。
     */
    public function testConstructorWithCustomApiContext(): void
    {
        $context = new ApiContext(true);
        $context->setToken('custom-token');

        $core = new Core(false, $context);

        $this->assertTrue($core->useSandbox());
        $this->assertEquals('custom-token', $core->getToken());
    }

    /**
     * 測試建構子接受自訂 HTTP Client。
     */
    public function testConstructorWithCustomHttpClient(): void
    {
        $mockClient = $this->createMock(ClientInterface::class);

        $core = new Core(false, null, $mockClient);

        $this->assertFalse($core->useSandbox());
    }

    /**
     * 測試建構子接受自訂 VoucherUseCaseFactory。
     */
    public function testConstructorWithCustomVoucherUseCaseFactory(): void
    {
        $mockFactory = $this->createMock(VoucherUseCaseFactory::class);

        $core = new Core(
            false,
            null,
            null,
            null,
            null,
            $mockFactory
        );

        $this->assertSame($mockFactory, $core->getVoucherUseCaseFactory());
    }

    /**
     * 測試建構子接受自訂 RewardsCardUseCaseFactory。
     */
    public function testConstructorWithCustomRewardsCardUseCaseFactory(): void
    {
        $mockFactory = $this->createMock(RewardsCardUseCaseFactory::class);

        $core = new Core(
            false,
            null,
            null,
            null,
            null,
            null,
            $mockFactory
        );

        $this->assertSame($mockFactory, $core->getRewardsCardUseCaseFactory());
    }

    /**
     * 測試 deepDeconstruction 處理簡單陣列。
     */
    public function testDeepDeconstructionWithSimpleArray(): void
    {
        $core = new Core();
        $input = ['key1' => 'value1', 'key2' => 'value2'];

        $result = $core->deepDeconstruction($input);

        $this->assertEquals($input, $result);
    }

    /**
     * 測試 deepDeconstruction 處理 RequestInterface。
     */
    public function testDeepDeconstructionWithRequestInterface(): void
    {
        $core = new Core();
        $request = new VoucherList();
        $request->lineId = 'test-line';

        $result = $core->deepDeconstruction($request);

        $this->assertIsArray($result);
        $this->assertEquals('test-line', $result['line_id']);
    }

    /**
     * 測試 deepDeconstruction 處理巢狀物件。
     */
    public function testDeepDeconstructionWithNestedObjects(): void
    {
        $core = new Core();
        $request = new CreateRedeemBatch();
        $request->phoneNumber = '0912345678';
        $request->storeOpenId = 'store-1';
        $request->posMacUid = 'pos-1';
        $request->batchList = [
            new Redeem(1, 'voucher-1', 2),
        ];

        $result = $core->deepDeconstruction($request);

        $this->assertIsArray($result);
        $this->assertEquals('0912345678', $result['phone_number']);
        $this->assertIsArray($result['batch_list']);
    }

    /**
     * 測試 voucher 方法使用 UseCase 物件。
     */
    public function testVoucherWithUseCaseObject(): void
    {
        $mockVoucherService = $this->createMock(VoucherService::class);
        $mockResponse = $this->createMock(ResponseInterface::class);

        $request = new VoucherList();
        $request->lineId = 'test-line';
        $useCase = new VoucherListUseCase($request);

        $mockVoucherService->expects($this->once())
            ->method('handle')
            ->with($useCase)
            ->willReturn($mockResponse);

        $core = new Core(false, null, null, $mockVoucherService);

        $result = $core->voucher($useCase);

        $this->assertSame($mockResponse, $result);
    }

    /**
     * 測試 voucher 方法使用 action 字串。
     */
    public function testVoucherWithActionString(): void
    {
        $mockVoucherService = $this->createMock(VoucherService::class);
        $mockResponse = new VoucherListResponse();

        $mockVoucherService->expects($this->once())
            ->method('handle')
            ->willReturn($mockResponse);

        $core = new Core(false, null, null, $mockVoucherService);

        $request = new VoucherList();
        $request->lineId = 'test';

        $result = $core->voucher('voucherList', $request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    /**
     * 測試 rewardsCard 方法使用 action 字串。
     */
    public function testRewardsCardWithActionString(): void
    {
        $mockRewardsCardService = $this->createMock(RewardsCardService::class);
        $mockResponse = new AccumulatePointResponse();

        $mockRewardsCardService->expects($this->once())
            ->method('handle')
            ->willReturn($mockResponse);

        $core = new Core(false, null, null, null, $mockRewardsCardService);

        $param = new AccumulatePoint();
        $param->phoneNumber = '0912345678';
        $param->amount = 100;

        $result = $core->rewardsCard('accumulatePoint', $param);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    /**
     * 測試 setToken 鏈式呼叫。
     */
    public function testSetTokenChaining(): void
    {
        $core = new Core(true);

        $result = $core->setToken('token-1')
            ->setToken('token-2')
            ->setToken('final-token');

        $this->assertSame($core, $result);
        $this->assertEquals('final-token', $core->getToken());
    }

    /**
     * 測試建構子建立完整的依賴鏈。
     */
    public function testConstructorCreatesCompleteDependencyChain(): void
    {
        $core = new Core(true);

        $this->assertTrue($core->useSandbox());
        $this->assertInstanceOf(VoucherUseCaseFactory::class, $core->getVoucherUseCaseFactory());
        $this->assertInstanceOf(RewardsCardUseCaseFactory::class, $core->getRewardsCardUseCaseFactory());
    }
}

