<?php

namespace Tests\Type\Request;

use CHYP\Partner\Echooss\Voucher\Type\Request\Redeem;
use CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface;
use PHPUnit\Framework\TestCase;

/**
 * Redeem Request 單元測試。
 */
class RedeemTest extends TestCase
{
    /**
     * 測試建構子預設參數。
     */
    public function testConstructorWithDefaultParameters(): void
    {
        $redeem = new Redeem();

        $this->assertEquals(1, $redeem->redeemType);
        $this->assertEquals('', $redeem->redeemId);
        $this->assertEquals(1, $redeem->redeemQuantity);
    }

    /**
     * 測試建構子自訂參數。
     */
    public function testConstructorWithCustomParameters(): void
    {
        $redeem = new Redeem(2, 'voucher-hash-123', 5);

        $this->assertEquals(2, $redeem->redeemType);
        $this->assertEquals('voucher-hash-123', $redeem->redeemId);
        $this->assertEquals(5, $redeem->redeemQuantity);
    }

    /**
     * 測試實作 RequestInterface。
     */
    public function testImplementsRequestInterface(): void
    {
        $redeem = new Redeem();

        $this->assertInstanceOf(RequestInterface::class, $redeem);
    }

    /**
     * 測試 toArray 輸出正確格式。
     */
    public function testToArrayOutputsCorrectFormat(): void
    {
        $redeem = new Redeem(1, 'abc123', 2);

        $array = $redeem->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('abc123', $array['redeem_id']);
        $this->assertEquals(2, $array['redeem_quantity']);
    }

    /**
     * 測試 redeemType 類型 1 (優惠券)。
     */
    public function testRedeemTypeCoupon(): void
    {
        $redeem = new Redeem();
        $redeem->redeemType = 1;

        $this->assertEquals(1, $redeem->redeemType);
    }

    /**
     * 測試 redeemType 類型 2 (商品券)。
     */
    public function testRedeemTypeProductVoucher(): void
    {
        $redeem = new Redeem();
        $redeem->redeemType = 2;

        $this->assertEquals(2, $redeem->redeemType);
    }

    /**
     * 測試設定 redeemId。
     */
    public function testSetRedeemId(): void
    {
        $redeem = new Redeem();
        $redeem->redeemId = 'new-voucher-id';

        $this->assertEquals('new-voucher-id', $redeem->redeemId);
    }

    /**
     * 測試設定 redeemQuantity。
     */
    public function testSetRedeemQuantity(): void
    {
        $redeem = new Redeem();
        $redeem->redeemQuantity = 10;

        $this->assertEquals(10, $redeem->redeemQuantity);
    }

    /**
     * 測試 toArray 輸出 snake_case 鍵名。
     */
    public function testToArrayOutputsSnakeCaseKeys(): void
    {
        $redeem = new Redeem(1, 'test', 3);

        $array = $redeem->toArray();

        $this->assertArrayHasKey('redeem_id', $array);
        $this->assertArrayHasKey('redeem_quantity', $array);
        $this->assertArrayNotHasKey('redeemId', $array);
        $this->assertArrayNotHasKey('redeemQuantity', $array);
    }

    /**
     * 測試建立多個 Redeem 物件。
     */
    public function testCreateMultipleRedeemObjects(): void
    {
        $redeem1 = new Redeem(1, 'voucher-1', 2);
        $redeem2 = new Redeem(2, 'voucher-2', 1);
        $redeem3 = new Redeem(1, 'voucher-3', 5);

        $this->assertEquals('voucher-1', $redeem1->redeemId);
        $this->assertEquals('voucher-2', $redeem2->redeemId);
        $this->assertEquals('voucher-3', $redeem3->redeemId);
    }

    /**
     * 測試空 redeemId。
     */
    public function testEmptyRedeemId(): void
    {
        $redeem = new Redeem(1, '', 1);

        $this->assertEquals('', $redeem->redeemId);
        $array = $redeem->toArray();
        $this->assertEquals('', $array['redeem_id']);
    }

    /**
     * 測試 toArray 完整輸出。
     */
    public function testToArrayCompleteOutput(): void
    {
        $redeem = new Redeem(2, 'complete-test', 3);

        $array = $redeem->toArray();

        // 驗證所有預期的欄位都存在
        $this->assertArrayHasKey('redeem_id', $array);
        $this->assertArrayHasKey('redeem_quantity', $array);
        $this->assertEquals('complete-test', $array['redeem_id']);
        $this->assertEquals(3, $array['redeem_quantity']);
    }
}

