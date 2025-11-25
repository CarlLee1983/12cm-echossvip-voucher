<?php

namespace Tests\Factory;

use CHYP\Partner\Echooss\Voucher\Application\Factory\VoucherUseCaseFactory;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\CreateRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\ExecuteRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\FreezeRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\QueryRedeemBatchDetailUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\QueryRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\ReverseRedeemUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\UpdateRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\VoucherListUseCase;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;
use CHYP\Partner\Echooss\Voucher\Type\Request\CreateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\ExecuteRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\FreezeRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\QueryRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\QueryRedeemBatchDetail;
use CHYP\Partner\Echooss\Voucher\Type\Request\ReverseRedeem;
use CHYP\Partner\Echooss\Voucher\Type\Request\UpdateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\VoucherList;
use PHPUnit\Framework\TestCase;

/**
 * VoucherUseCaseFactory 單元測試。
 */
class VoucherUseCaseFactoryTest extends TestCase
{
    protected VoucherUseCaseFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new VoucherUseCaseFactory();
    }

    /**
     * 測試建立 VoucherListUseCase。
     */
    public function testCreateVoucherListUseCase(): void
    {
        $request = new VoucherList();
        $request->lineId = 'test-line-id';

        $useCase = $this->factory->create('voucherList', $request);

        $this->assertInstanceOf(VoucherListUseCase::class, $useCase);
        $this->assertEquals('/pos_gateway/api/voucher-list', $useCase->path());
        $this->assertEquals('voucherList', $useCase->responseType());
    }

    /**
     * 測試建立 CreateRedeemBatchUseCase。
     */
    public function testCreateRedeemBatchUseCase(): void
    {
        $request = new CreateRedeemBatch();

        $useCase = $this->factory->create('createRedeemBatch', $request);

        $this->assertInstanceOf(CreateRedeemBatchUseCase::class, $useCase);
        $this->assertEquals('createRedeemBatch', $useCase->responseType());
    }

    /**
     * 測試建立 QueryRedeemBatchUseCase。
     */
    public function testCreateQueryRedeemBatchUseCase(): void
    {
        $request = new QueryRedeemBatch();

        $useCase = $this->factory->create('queryRedeemBatch', $request);

        $this->assertInstanceOf(QueryRedeemBatchUseCase::class, $useCase);
    }

    /**
     * 測試建立 QueryRedeemBatchDetailUseCase。
     */
    public function testCreateQueryRedeemBatchDetailUseCase(): void
    {
        $request = new QueryRedeemBatchDetail();

        $useCase = $this->factory->create('queryRedeemBatchDetail', $request);

        $this->assertInstanceOf(QueryRedeemBatchDetailUseCase::class, $useCase);
    }

    /**
     * 測試建立 FreezeRedeemBatchUseCase。
     */
    public function testCreateFreezeRedeemBatchUseCase(): void
    {
        $request = new FreezeRedeemBatch();

        $useCase = $this->factory->create('freezeRedeemBatch', $request);

        $this->assertInstanceOf(FreezeRedeemBatchUseCase::class, $useCase);
    }

    /**
     * 測試建立 UpdateRedeemBatchUseCase。
     */
    public function testCreateUpdateRedeemBatchUseCase(): void
    {
        $request = new UpdateRedeemBatch();

        $useCase = $this->factory->create('updateRedeemBatch', $request);

        $this->assertInstanceOf(UpdateRedeemBatchUseCase::class, $useCase);
    }

    /**
     * 測試建立 ExecuteRedeemBatchUseCase。
     */
    public function testCreateExecuteRedeemBatchUseCase(): void
    {
        $request = new ExecuteRedeemBatch();

        $useCase = $this->factory->create('executeRedeemBatch', $request);

        $this->assertInstanceOf(ExecuteRedeemBatchUseCase::class, $useCase);
    }

    /**
     * 測試建立 ReverseRedeemUseCase。
     */
    public function testCreateReverseRedeemUseCase(): void
    {
        $request = new ReverseRedeem();

        $useCase = $this->factory->create('reverseRedeem', $request);

        $this->assertInstanceOf(ReverseRedeemUseCase::class, $useCase);
    }

    /**
     * 測試無效的 action 應該拋出例外。
     */
    public function testInvalidActionThrowsException(): void
    {
        $request = new VoucherList();

        $this->expectException(RequestTypeException::class);
        $this->expectExceptionMessage('Request action "invalidAction" not exists');

        $this->factory->create('invalidAction', $request);
    }

    /**
     * 測試 null payload 應該拋出例外。
     */
    public function testNullPayloadThrowsException(): void
    {
        $this->expectException(RequestTypeException::class);
        $this->expectExceptionMessage('Voucher request payload is required');

        $this->factory->create('voucherList', null);
    }

    /**
     * 測試非 RequestInterface payload 應該拋出例外。
     */
    public function testInvalidPayloadThrowsException(): void
    {
        $this->expectException(RequestTypeException::class);

        $this->factory->create('voucherList', ['invalid' => 'array']);
    }

    /**
     * 測試 supports 方法。
     */
    public function testSupports(): void
    {
        $this->assertTrue($this->factory->supports('voucherList'));
        $this->assertTrue($this->factory->supports('createRedeemBatch'));
        $this->assertTrue($this->factory->supports('reverseRedeem'));

        $this->assertFalse($this->factory->supports('invalidAction'));
        $this->assertFalse($this->factory->supports(''));
    }

    /**
     * 測試 supportedActions 方法。
     */
    public function testSupportedActions(): void
    {
        $actions = $this->factory->supportedActions();

        $this->assertContains('voucherList', $actions);
        $this->assertContains('createRedeemBatch', $actions);
        $this->assertContains('queryRedeemBatch', $actions);
        $this->assertContains('queryRedeemBatchDetail', $actions);
        $this->assertContains('freezeRedeemBatch', $actions);
        $this->assertContains('updateRedeemBatch', $actions);
        $this->assertContains('executeRedeemBatch', $actions);
        $this->assertContains('reverseRedeem', $actions);
        $this->assertCount(8, $actions);
    }
}
