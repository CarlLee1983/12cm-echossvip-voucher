<?php

namespace Tests\Application\UseCase\Voucher;

use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\CreateRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\ExecuteRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\FreezeRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\QueryRedeemBatchDetailUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\QueryRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\ReverseRedeemUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\UpdateRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\VoucherListUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\VoucherUseCaseInterface;
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
 * Voucher UseCase 單元測試。
 */
class VoucherUseCaseTest extends TestCase
{
    /**
     * 測試 VoucherListUseCase。
     */
    public function testVoucherListUseCase(): void
    {
        $request = new VoucherList();
        $request->lineId = 'test-line-id';

        $useCase = new VoucherListUseCase($request);

        $this->assertInstanceOf(VoucherUseCaseInterface::class, $useCase);
        $this->assertEquals('/pos_gateway/api/voucher-list', $useCase->path());
        $this->assertEquals('voucherList', $useCase->responseType());
        $this->assertSame($request, $useCase->payload());
    }

    /**
     * 測試 CreateRedeemBatchUseCase。
     */
    public function testCreateRedeemBatchUseCase(): void
    {
        $request = new CreateRedeemBatch();
        $request->phoneNumber = '0912345678';
        $request->storeOpenId = 'store-1';

        $useCase = new CreateRedeemBatchUseCase($request);

        $this->assertInstanceOf(VoucherUseCaseInterface::class, $useCase);
        $this->assertEquals('/pos_gateway/api/create-redeem-batch', $useCase->path());
        $this->assertEquals('createRedeemBatch', $useCase->responseType());
        $this->assertSame($request, $useCase->payload());
    }

    /**
     * 測試 QueryRedeemBatchUseCase。
     */
    public function testQueryRedeemBatchUseCase(): void
    {
        $request = new QueryRedeemBatch();
        $request->batchToken = 'token-123';

        $useCase = new QueryRedeemBatchUseCase($request);

        $this->assertInstanceOf(VoucherUseCaseInterface::class, $useCase);
        $this->assertEquals('/pos_gateway/api/query-redeem-batch', $useCase->path());
        $this->assertEquals('queryRedeemBatch', $useCase->responseType());
        $this->assertSame($request, $useCase->payload());
    }

    /**
     * 測試 QueryRedeemBatchDetailUseCase。
     */
    public function testQueryRedeemBatchDetailUseCase(): void
    {
        $request = new QueryRedeemBatchDetail();
        $request->batchUuid = 'uuid-456';

        $useCase = new QueryRedeemBatchDetailUseCase($request);

        $this->assertInstanceOf(VoucherUseCaseInterface::class, $useCase);
        $this->assertEquals('/pos_gateway/api/query-redeem-batch-detail', $useCase->path());
        $this->assertEquals('queryRedeemBatchDetail', $useCase->responseType());
        $this->assertSame($request, $useCase->payload());
    }

    /**
     * 測試 FreezeRedeemBatchUseCase。
     */
    public function testFreezeRedeemBatchUseCase(): void
    {
        $request = new FreezeRedeemBatch();
        $request->batchUuid = 'uuid-789';
        $request->freezeMins = 10;

        $useCase = new FreezeRedeemBatchUseCase($request);

        $this->assertInstanceOf(VoucherUseCaseInterface::class, $useCase);
        $this->assertEquals('/pos_gateway/api/freeze-redeem-batch', $useCase->path());
        $this->assertEquals('freezeRedeemBatch', $useCase->responseType());
        $this->assertSame($request, $useCase->payload());
    }

    /**
     * 測試 UpdateRedeemBatchUseCase。
     */
    public function testUpdateRedeemBatchUseCase(): void
    {
        $request = new UpdateRedeemBatch();
        $request->batchUuid = 'uuid-update';

        $useCase = new UpdateRedeemBatchUseCase($request);

        $this->assertInstanceOf(VoucherUseCaseInterface::class, $useCase);
        $this->assertEquals('/pos_gateway/api/update-redeem-batch', $useCase->path());
        $this->assertEquals('updateRedeemBatch', $useCase->responseType());
        $this->assertSame($request, $useCase->payload());
    }

    /**
     * 測試 ExecuteRedeemBatchUseCase。
     */
    public function testExecuteRedeemBatchUseCase(): void
    {
        $request = new ExecuteRedeemBatch();
        $request->batchUuid = 'uuid-execute';

        $useCase = new ExecuteRedeemBatchUseCase($request);

        $this->assertInstanceOf(VoucherUseCaseInterface::class, $useCase);
        $this->assertEquals('/pos_gateway/api/execute-redeem-batch', $useCase->path());
        $this->assertEquals('executeRedeemBatch', $useCase->responseType());
        $this->assertSame($request, $useCase->payload());
    }

    /**
     * 測試 ReverseRedeemUseCase。
     */
    public function testReverseRedeemUseCase(): void
    {
        $request = new ReverseRedeem();
        $request->phoneNumber = '0912345678';

        $useCase = new ReverseRedeemUseCase($request);

        $this->assertInstanceOf(VoucherUseCaseInterface::class, $useCase);
        $this->assertEquals('/pos_gateway/api/reverse-redeem', $useCase->path());
        $this->assertEquals('reverseRedeem', $useCase->responseType());
        $this->assertSame($request, $useCase->payload());
    }

    /**
     * 使用 Data Provider 測試所有 UseCase 的基本功能。
     *
     * @param string $useCaseClass    UseCase 類別名稱。
     * @param string $requestClass    Request 類別名稱。
     * @param string $expectedPath    期望的 API 路徑。
     * @param string $expectedType    期望的回應類型。
     *
     * @dataProvider useCaseProvider
     */
    public function testUseCaseImplementsInterface(
        string $useCaseClass,
        string $requestClass,
        string $expectedPath,
        string $expectedType
    ): void {
        $request = new $requestClass();
        $useCase = new $useCaseClass($request);

        $this->assertInstanceOf(VoucherUseCaseInterface::class, $useCase);
        $this->assertEquals($expectedPath, $useCase->path());
        $this->assertEquals($expectedType, $useCase->responseType());
    }

    /**
     * 提供所有 UseCase 測試資料。
     *
     * @return array
     */
    public function useCaseProvider(): array
    {
        return [
            [
                VoucherListUseCase::class,
                VoucherList::class,
                '/pos_gateway/api/voucher-list',
                'voucherList',
            ],
            [
                CreateRedeemBatchUseCase::class,
                CreateRedeemBatch::class,
                '/pos_gateway/api/create-redeem-batch',
                'createRedeemBatch',
            ],
            [
                QueryRedeemBatchUseCase::class,
                QueryRedeemBatch::class,
                '/pos_gateway/api/query-redeem-batch',
                'queryRedeemBatch',
            ],
            [
                QueryRedeemBatchDetailUseCase::class,
                QueryRedeemBatchDetail::class,
                '/pos_gateway/api/query-redeem-batch-detail',
                'queryRedeemBatchDetail',
            ],
            [
                FreezeRedeemBatchUseCase::class,
                FreezeRedeemBatch::class,
                '/pos_gateway/api/freeze-redeem-batch',
                'freezeRedeemBatch',
            ],
            [
                UpdateRedeemBatchUseCase::class,
                UpdateRedeemBatch::class,
                '/pos_gateway/api/update-redeem-batch',
                'updateRedeemBatch',
            ],
            [
                ExecuteRedeemBatchUseCase::class,
                ExecuteRedeemBatch::class,
                '/pos_gateway/api/execute-redeem-batch',
                'executeRedeemBatch',
            ],
            [
                ReverseRedeemUseCase::class,
                ReverseRedeem::class,
                '/pos_gateway/api/reverse-redeem',
                'reverseRedeem',
            ],
        ];
    }

    /**
     * 測試 payload 回傳原始 request 物件。
     */
    public function testPayloadReturnsSameRequestObject(): void
    {
        $request = new VoucherList();
        $request->lineId = 'unique-id';

        $useCase = new VoucherListUseCase($request);

        $payload = $useCase->payload();

        $this->assertSame($request, $payload);
        $this->assertEquals('unique-id', $payload->lineId);
    }
}

