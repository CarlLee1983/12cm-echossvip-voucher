<?php

namespace Tests\Application\Hydrator;

use CHYP\Partner\Echooss\Voucher\Application\Factory\ResponseFactory;
use CHYP\Partner\Echooss\Voucher\Application\Hydrator\ResponseHydrator;
use CHYP\Partner\Echooss\Voucher\Type\Response\AccumulatePoint;
use CHYP\Partner\Echooss\Voucher\Type\Response\CreateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface;
use PHPUnit\Framework\TestCase;

/**
 * ResponseHydrator 單元測試。
 */
class ResponseHydratorTest extends TestCase
{
    protected ResponseHydrator $hydrator;

    protected function setUp(): void
    {
        $factory = new ResponseFactory();
        $this->hydrator = new ResponseHydrator($factory);
    }

    /**
     * 測試 hydrate 方法回傳正確的 ResponseInterface。
     */
    public function testHydrateReturnsResponseInterface(): void
    {
        $payload = ['success' => true, 'message' => 'test'];

        $response = $this->hydrator->hydrate('createRedeemBatch', $payload);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertInstanceOf(CreateRedeemBatch::class, $response);
    }

    /**
     * 測試 hydrate 方法正確設定屬性值。
     */
    public function testHydrateSetsPropertiesCorrectly(): void
    {
        $payload = [
            'success' => true,
            'message' => 'Successfully created',
            'batch_token' => 'ABC123',
            'batch_uuid' => 'uuid-456',
        ];

        $response = $this->hydrator->hydrate('createRedeemBatch', $payload);

        $this->assertTrue($response->success);
        $this->assertEquals('Successfully created', $response->message);
        $this->assertEquals('ABC123', $response->batchToken);
        $this->assertEquals('uuid-456', $response->batchUuid);
    }

    /**
     * 測試 snake_case 欄位轉換為 camelCase。
     */
    public function testHydrateConvertsSnakeCaseToCamelCase(): void
    {
        $payload = [
            'batch_token' => 'token123',
            'batch_uuid' => 'uuid456',
            'store_open_id' => 'store789',
        ];

        $response = $this->hydrator->hydrate('createRedeemBatch', $payload);

        $this->assertEquals('token123', $response->batchToken);
        $this->assertEquals('uuid456', $response->batchUuid);
    }

    /**
     * 測試 hydrate AccumulatePoint 回應。
     */
    public function testHydrateAccumulatePointResponse(): void
    {
        $payload = [
            'message' => 'Points accumulated',
            'point' => 100,
            'amount' => 1000,
        ];

        $response = $this->hydrator->hydrate('accumulatePoint', $payload);

        $this->assertInstanceOf(AccumulatePoint::class, $response);
        $this->assertEquals('Points accumulated', $response->message);
        $this->assertEquals(100, $response->point);
        $this->assertEquals(1000, $response->amount);
    }

    /**
     * 測試空 payload 的處理。
     */
    public function testHydrateWithEmptyPayload(): void
    {
        $response = $this->hydrator->hydrate('createRedeemBatch', []);

        $this->assertInstanceOf(CreateRedeemBatch::class, $response);
    }

    /**
     * 測試數值型欄位名稱的處理。
     */
    public function testHydrateWithNumericKeys(): void
    {
        $payload = [
            0 => 'value0',
            1 => 'value1',
            'success' => true,
        ];

        $response = $this->hydrator->hydrate('createRedeemBatch', $payload);

        $this->assertTrue($response->success);
    }

    /**
     * 測試巢狀陣列的處理。
     */
    public function testHydrateWithNestedArray(): void
    {
        $payload = [
            'success' => true,
            'details' => [
                ['id' => 1, 'name' => 'Item 1'],
                ['id' => 2, 'name' => 'Item 2'],
            ],
        ];

        $response = $this->hydrator->hydrate('queryRedeemBatchDetail', $payload);

        $this->assertTrue($response->success);
        $this->assertIsArray($response->details);
        $this->assertCount(2, $response->details);
    }

    /**
     * 使用 Mock Factory 測試 hydrator。
     */
    public function testHydrateWithMockedFactory(): void
    {
        $mockFactory = $this->createMock(ResponseFactory::class);
        $mockResponse = $this->createMock(ResponseInterface::class);

        $mockFactory->expects($this->once())
            ->method('create')
            ->with('testType')
            ->willReturn($mockResponse);

        $hydrator = new ResponseHydrator($mockFactory);
        $result = $hydrator->hydrate('testType', []);

        $this->assertSame($mockResponse, $result);
    }
}

