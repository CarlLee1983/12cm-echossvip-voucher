<?php

namespace Tests\Type\Response;

use CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface;
use CHYP\Partner\Echooss\Voucher\Type\Response\Voucher;
use CHYP\Partner\Echooss\Voucher\Type\Response\VoucherList;
use PHPUnit\Framework\TestCase;

/**
 * VoucherList Response 單元測試。
 */
class VoucherListTest extends TestCase
{
    /**
     * 測試實作 ResponseInterface。
     */
    public function testImplementsResponseInterface(): void
    {
        $voucherList = new VoucherList();

        $this->assertInstanceOf(ResponseInterface::class, $voucherList);
    }

    /**
     * 測試 data 預設為空陣列。
     */
    public function testDataDefaultsToEmptyArray(): void
    {
        $voucherList = new VoucherList();

        $this->assertIsArray($voucherList->data);
        $this->assertEmpty($voucherList->data);
    }

    /**
     * 測試 toArray 回傳 data 內容。
     */
    public function testToArrayReturnsData(): void
    {
        $voucherList = new VoucherList();
        $voucherList->data = ['item1', 'item2'];

        $array = $voucherList->toArray();

        $this->assertEquals(['item1', 'item2'], $array);
    }

    /**
     * 測試 data 方法轉換 Voucher 物件。
     */
    public function testDataMethodConvertsToVoucherObjects(): void
    {
        $voucherList = new VoucherList();
        $rows = [
            ['type' => 1, 'voucher_hash_id' => 'abc123', 'name' => 'Test Voucher 1'],
            ['type' => 2, 'voucher_hash_id' => 'def456', 'name' => 'Test Voucher 2'],
        ];

        $vouchers = $voucherList->data($rows);

        $this->assertIsArray($vouchers);
        $this->assertCount(2, $vouchers);
        $this->assertInstanceOf(Voucher::class, $vouchers[0]);
        $this->assertInstanceOf(Voucher::class, $vouchers[1]);
    }

    /**
     * 測試 data 方法正確設定屬性。
     */
    public function testDataMethodSetsPropertiesCorrectly(): void
    {
        $voucherList = new VoucherList();
        $rows = [
            [
                'type' => 1,
                'voucher_hash_id' => 'hash123',
                'name' => 'My Voucher',
                'total_count' => 10,
                'redeemable_count' => 5,
            ],
        ];

        $vouchers = $voucherList->data($rows);

        $this->assertEquals(1, $vouchers[0]->type);
        $this->assertEquals('hash123', $vouchers[0]->voucherHashId);
        $this->assertEquals('My Voucher', $vouchers[0]->name);
        $this->assertEquals(10, $vouchers[0]->totalCount);
        $this->assertEquals(5, $vouchers[0]->redeemableCount);
    }

    /**
     * 測試 data 方法處理空陣列。
     */
    public function testDataMethodWithEmptyArray(): void
    {
        $voucherList = new VoucherList();

        $vouchers = $voucherList->data([]);

        $this->assertIsArray($vouchers);
        $this->assertEmpty($vouchers);
    }

    /**
     * 測試 data 方法轉換 snake_case 為 camelCase。
     */
    public function testDataMethodConvertsSnakeCaseToCamelCase(): void
    {
        $voucherList = new VoucherList();
        $rows = [
            [
                'voucher_hash_id' => 'test-hash',
                'period_sales' => 1,
                'sales_start_date' => '2023-01-01',
                'sales_end_date' => '2023-12-31',
                'total_count' => 100,
                'unusable_count' => 10,
                'redeemable_count' => 90,
            ],
        ];

        $vouchers = $voucherList->data($rows);

        $this->assertEquals('test-hash', $vouchers[0]->voucherHashId);
        $this->assertEquals(1, $vouchers[0]->periodSales);
        // 日期欄位會被轉換成 DateTimeImmutable
        $this->assertInstanceOf(\DateTimeImmutable::class, $vouchers[0]->salesStartDate);
        $this->assertInstanceOf(\DateTimeImmutable::class, $vouchers[0]->salesEndDate);
        $this->assertEquals(100, $vouchers[0]->totalCount);
        $this->assertEquals(10, $vouchers[0]->unusableCount);
        $this->assertEquals(90, $vouchers[0]->redeemableCount);
    }

    /**
     * 測試設定 data 屬性。
     */
    public function testSetDataProperty(): void
    {
        $voucherList = new VoucherList();
        $voucher1 = new Voucher();
        $voucher1->name = 'Voucher 1';
        $voucher2 = new Voucher();
        $voucher2->name = 'Voucher 2';

        $voucherList->data = [$voucher1, $voucher2];

        $this->assertCount(2, $voucherList->data);
        $this->assertEquals('Voucher 1', $voucherList->data[0]->name);
        $this->assertEquals('Voucher 2', $voucherList->data[1]->name);
    }

    /**
     * 測試 toArray 輸出 Voucher 物件陣列。
     */
    public function testToArrayWithVoucherObjects(): void
    {
        $voucherList = new VoucherList();
        $voucher = new Voucher();
        $voucher->name = 'Test';
        $voucherList->data = [$voucher];

        $array = $voucherList->toArray();

        $this->assertCount(1, $array);
        $this->assertInstanceOf(Voucher::class, $array[0]);
    }

    /**
     * 測試 data 方法處理含 null 值的資料。
     */
    public function testDataMethodWithNullValues(): void
    {
        $voucherList = new VoucherList();
        $rows = [
            [
                'type' => 1,
                'voucher_hash_id' => 'abc',
                'name' => 'Test',
                'term_id' => null,
            ],
        ];

        $vouchers = $voucherList->data($rows);

        $this->assertNull($vouchers[0]->termId);
    }

    /**
     * 測試 data 方法處理含圖片資料。
     */
    public function testDataMethodWithImages(): void
    {
        $voucherList = new VoucherList();
        $rows = [
            [
                'type' => 1,
                'name' => 'Test',
                'images' => [
                    ['id' => 1, 'url' => 'http://example.com/1.jpg', 'order' => 1],
                    ['id' => 2, 'url' => 'http://example.com/2.jpg', 'order' => 2],
                ],
            ],
        ];

        $vouchers = $voucherList->data($rows);

        $this->assertIsArray($vouchers[0]->images);
        $this->assertCount(2, $vouchers[0]->images);
    }

    /**
     * 測試多筆資料轉換。
     */
    public function testDataMethodWithMultipleRows(): void
    {
        $voucherList = new VoucherList();
        $rows = [];
        for ($i = 0; $i < 5; $i++) {
            $rows[] = [
                'type' => 1,
                'voucher_hash_id' => 'hash-' . $i,
                'name' => 'Voucher ' . $i,
            ];
        }

        $vouchers = $voucherList->data($rows);

        $this->assertCount(5, $vouchers);
        for ($i = 0; $i < 5; $i++) {
            $this->assertEquals('hash-' . $i, $vouchers[$i]->voucherHashId);
            $this->assertEquals('Voucher ' . $i, $vouchers[$i]->name);
        }
    }
}

