<?php

namespace Tests\Application\Factory;

use CHYP\Partner\Echooss\Voucher\Application\Factory\ResponseFactory;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;
use CHYP\Partner\Echooss\Voucher\Type\Response\AccumulatePoint;
use CHYP\Partner\Echooss\Voucher\Type\Response\CreateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\DepletePoint;
use CHYP\Partner\Echooss\Voucher\Type\Response\ExecuteRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\FreezeRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\QueryRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\QueryRedeemBatchDetail;
use CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface;
use CHYP\Partner\Echooss\Voucher\Type\Response\ReverseRedeem;
use CHYP\Partner\Echooss\Voucher\Type\Response\UpdateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\Voucher;
use CHYP\Partner\Echooss\Voucher\Type\Response\VoucherList;
use PHPUnit\Framework\TestCase;

/**
 * ResponseFactory 單元測試。
 */
class ResponseFactoryTest extends TestCase
{
    protected ResponseFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ResponseFactory();
    }

    /**
     * 測試建立 VoucherList 回應。
     */
    public function testCreateVoucherList(): void
    {
        $response = $this->factory->create('voucherList');

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertInstanceOf(VoucherList::class, $response);
    }

    /**
     * 測試建立 CreateRedeemBatch 回應。
     */
    public function testCreateCreateRedeemBatch(): void
    {
        $response = $this->factory->create('createRedeemBatch');

        $this->assertInstanceOf(CreateRedeemBatch::class, $response);
    }

    /**
     * 測試建立 QueryRedeemBatch 回應。
     */
    public function testCreateQueryRedeemBatch(): void
    {
        $response = $this->factory->create('queryRedeemBatch');

        $this->assertInstanceOf(QueryRedeemBatch::class, $response);
    }

    /**
     * 測試建立 QueryRedeemBatchDetail 回應。
     */
    public function testCreateQueryRedeemBatchDetail(): void
    {
        $response = $this->factory->create('queryRedeemBatchDetail');

        $this->assertInstanceOf(QueryRedeemBatchDetail::class, $response);
    }

    /**
     * 測試建立 FreezeRedeemBatch 回應。
     */
    public function testCreateFreezeRedeemBatch(): void
    {
        $response = $this->factory->create('freezeRedeemBatch');

        $this->assertInstanceOf(FreezeRedeemBatch::class, $response);
    }

    /**
     * 測試建立 UpdateRedeemBatch 回應。
     */
    public function testCreateUpdateRedeemBatch(): void
    {
        $response = $this->factory->create('updateRedeemBatch');

        $this->assertInstanceOf(UpdateRedeemBatch::class, $response);
    }

    /**
     * 測試建立 ExecuteRedeemBatch 回應。
     */
    public function testCreateExecuteRedeemBatch(): void
    {
        $response = $this->factory->create('executeRedeemBatch');

        $this->assertInstanceOf(ExecuteRedeemBatch::class, $response);
    }

    /**
     * 測試建立 ReverseRedeem 回應。
     */
    public function testCreateReverseRedeem(): void
    {
        $response = $this->factory->create('reverseRedeem');

        $this->assertInstanceOf(ReverseRedeem::class, $response);
    }

    /**
     * 測試建立 AccumulatePoint 回應。
     */
    public function testCreateAccumulatePoint(): void
    {
        $response = $this->factory->create('accumulatePoint');

        $this->assertInstanceOf(AccumulatePoint::class, $response);
    }

    /**
     * 測試建立 DepletePoint 回應。
     */
    public function testCreateDepletePoint(): void
    {
        $response = $this->factory->create('depletePoint');

        $this->assertInstanceOf(DepletePoint::class, $response);
    }

    /**
     * 測試建立 Voucher 回應。
     */
    public function testCreateVoucher(): void
    {
        $response = $this->factory->create('voucher');

        $this->assertInstanceOf(Voucher::class, $response);
    }

    /**
     * 測試無效類型拋出例外。
     */
    public function testCreateWithInvalidTypeThrowsException(): void
    {
        $this->expectException(RequestTypeException::class);
        $this->expectExceptionMessage('Response type not registered. type: invalidType');

        $this->factory->create('invalidType');
    }

    /**
     * 測試空字串類型拋出例外。
     */
    public function testCreateWithEmptyTypeThrowsException(): void
    {
        $this->expectException(RequestTypeException::class);

        $this->factory->create('');
    }

    /**
     * 使用 Data Provider 測試所有已註冊類型。
     *
     * @param string $type          Response type key.
     * @param string $expectedClass Expected response class.
     *
     * @dataProvider registeredTypesProvider
     */
    public function testAllRegisteredTypes(string $type, string $expectedClass): void
    {
        $response = $this->factory->create($type);

        $this->assertInstanceOf($expectedClass, $response);
    }

    /**
     * 提供所有已註冊的回應類型。
     *
     * @return array
     */
    public function registeredTypesProvider(): array
    {
        return [
            ['voucherList', VoucherList::class],
            ['createRedeemBatch', CreateRedeemBatch::class],
            ['queryRedeemBatch', QueryRedeemBatch::class],
            ['queryRedeemBatchDetail', QueryRedeemBatchDetail::class],
            ['freezeRedeemBatch', FreezeRedeemBatch::class],
            ['updateRedeemBatch', UpdateRedeemBatch::class],
            ['executeRedeemBatch', ExecuteRedeemBatch::class],
            ['reverseRedeem', ReverseRedeem::class],
            ['accumulatePoint', AccumulatePoint::class],
            ['depletePoint', DepletePoint::class],
            ['voucher', Voucher::class],
        ];
    }
}

