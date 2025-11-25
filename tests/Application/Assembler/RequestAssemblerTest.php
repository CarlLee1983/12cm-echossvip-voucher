<?php

namespace Tests\Application\Assembler;

use CHYP\Partner\Echooss\Voucher\Application\Assembler\RequestAssembler;
use CHYP\Partner\Echooss\Voucher\Type\Request\CreateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\Redeem;
use CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface;
use CHYP\Partner\Echooss\Voucher\Type\Request\VoucherList;
use CHYP\Partner\Echooss\Voucher\Type\Response\CreateRedeemBatch as CreateRedeemBatchResponse;
use CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface;
use PHPUnit\Framework\TestCase;

/**
 * RequestAssembler 單元測試。
 */
class RequestAssemblerTest extends TestCase
{
    protected RequestAssembler $assembler;

    protected function setUp(): void
    {
        $this->assembler = new RequestAssembler();
    }

    /**
     * 測試簡單陣列直接回傳。
     */
    public function testToArrayWithSimpleArray(): void
    {
        $input = ['key1' => 'value1', 'key2' => 'value2'];

        $result = $this->assembler->toArray($input);

        $this->assertEquals($input, $result);
    }

    /**
     * 測試巢狀陣列遞迴轉換。
     */
    public function testToArrayWithNestedArray(): void
    {
        $input = [
            'level1' => [
                'level2' => [
                    'level3' => 'value',
                ],
            ],
        ];

        $result = $this->assembler->toArray($input);

        $this->assertEquals($input, $result);
    }

    /**
     * 測試 RequestInterface 物件轉換為陣列。
     */
    public function testToArrayWithRequestInterface(): void
    {
        $request = new VoucherList();
        $request->lineId = 'test-line-id';

        $result = $this->assembler->toArray($request);

        $this->assertIsArray($result);
        $this->assertEquals('test-line-id', $result['line_id']);
    }

    /**
     * 測試 ResponseInterface 物件轉換為陣列。
     */
    public function testToArrayWithResponseInterface(): void
    {
        $response = new CreateRedeemBatchResponse();
        $response->success = true;
        $response->message = 'Test message';
        $response->batchToken = 'token123';

        $result = $this->assembler->toArray($response);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEquals('Test message', $result['message']);
    }

    /**
     * 測試陣列內含 RequestInterface 物件。
     */
    public function testToArrayWithArrayContainingRequestInterface(): void
    {
        $request1 = new VoucherList();
        $request1->lineId = 'line-1';

        $request2 = new VoucherList();
        $request2->lineId = 'line-2';

        $input = [$request1, $request2];

        $result = $this->assembler->toArray($input);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('line-1', $result[0]['line_id']);
        $this->assertEquals('line-2', $result[1]['line_id']);
    }

    /**
     * 測試純量值直接回傳。
     */
    public function testToArrayWithScalarValue(): void
    {
        $this->assertEquals('string', $this->assembler->toArray('string'));
        $this->assertEquals(123, $this->assembler->toArray(123));
        $this->assertEquals(12.5, $this->assembler->toArray(12.5));
        $this->assertTrue($this->assembler->toArray(true));
        $this->assertNull($this->assembler->toArray(null));
    }

    /**
     * 測試空陣列。
     */
    public function testToArrayWithEmptyArray(): void
    {
        $result = $this->assembler->toArray([]);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * 測試複雜巢狀結構（含 RequestInterface）。
     */
    public function testToArrayWithComplexNestedStructure(): void
    {
        $request = new CreateRedeemBatch();
        $request->phoneNumber = '0912345678';
        $request->storeOpenId = 'store-123';
        $request->posMacUid = 'pos-456';
        $request->batchList = [
            new Redeem(1, 'voucher-1', 2),
            new Redeem(2, 'voucher-2', 1),
        ];

        $result = $this->assembler->toArray($request);

        $this->assertIsArray($result);
        $this->assertEquals('0912345678', $result['phone_number']);
        $this->assertEquals('store-123', $result['store_open_id']);
        $this->assertEquals('pos-456', $result['pos_mac_uid']);
        $this->assertIsArray($result['batch_list']);
        $this->assertCount(2, $result['batch_list']);
    }

    /**
     * 測試混合陣列和物件。
     */
    public function testToArrayWithMixedArrayAndObjects(): void
    {
        $request = new VoucherList();
        $request->lineId = 'test-line';

        $input = [
            'simple_key' => 'simple_value',
            'nested' => [
                'request' => $request,
                'array' => ['a', 'b', 'c'],
            ],
        ];

        $result = $this->assembler->toArray($input);

        $this->assertEquals('simple_value', $result['simple_key']);
        $this->assertIsArray($result['nested']['request']);
        $this->assertEquals('test-line', $result['nested']['request']['line_id']);
        $this->assertEquals(['a', 'b', 'c'], $result['nested']['array']);
    }

    /**
     * 使用 Mock 測試 RequestInterface。
     */
    public function testToArrayWithMockedRequestInterface(): void
    {
        $mockRequest = $this->createMock(RequestInterface::class);
        $mockRequest->expects($this->once())
            ->method('toArray')
            ->willReturn(['mocked' => 'value']);

        $result = $this->assembler->toArray($mockRequest);

        $this->assertEquals(['mocked' => 'value'], $result);
    }

    /**
     * 使用 Mock 測試 ResponseInterface。
     */
    public function testToArrayWithMockedResponseInterface(): void
    {
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->expects($this->once())
            ->method('toArray')
            ->willReturn(['response' => 'data']);

        $result = $this->assembler->toArray($mockResponse);

        $this->assertEquals(['response' => 'data'], $result);
    }
}

