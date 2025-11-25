<?php

namespace Tests\Application;

use CHYP\Partner\Echooss\Voucher\Application\ApiContext;
use PHPUnit\Framework\TestCase;

/**
 * ApiContext 單元測試。
 */
class ApiContextTest extends TestCase
{
    /**
     * 測試預設建構子使用 production 環境。
     */
    public function testDefaultConstructorUsesProduction(): void
    {
        $context = new ApiContext();

        $this->assertFalse($context->useSandbox());
    }

    /**
     * 測試建構子設定 sandbox 模式。
     */
    public function testConstructorWithSandboxEnabled(): void
    {
        $context = new ApiContext(true);

        $this->assertTrue($context->useSandbox());
    }

    /**
     * 測試 setSandbox 方法可切換環境。
     */
    public function testSetSandboxTogglesSandboxMode(): void
    {
        $context = new ApiContext(false);

        $this->assertFalse($context->useSandbox());

        $result = $context->setSandbox(true);

        $this->assertTrue($context->useSandbox());
        $this->assertSame($context, $result); // 測試 fluent interface
    }

    /**
     * 測試 token 預設為空字串。
     */
    public function testTokenDefaultsToEmptyString(): void
    {
        $context = new ApiContext();

        $this->assertEquals('', $context->token());
    }

    /**
     * 測試 setToken 方法設定 token。
     */
    public function testSetTokenSetsToken(): void
    {
        $context = new ApiContext();
        $token = 'test-bearer-token-12345';

        $result = $context->setToken($token);

        $this->assertEquals($token, $context->token());
        $this->assertSame($context, $result); // 測試 fluent interface
    }

    /**
     * 測試 voucherBaseUri 在 production 模式下回傳正確 URI。
     */
    public function testVoucherBaseUriReturnsProductionHost(): void
    {
        $context = new ApiContext(false);

        $this->assertEquals('https://service.12cm.com.tw', $context->voucherBaseUri());
    }

    /**
     * 測試 voucherBaseUri 在 sandbox 模式下回傳正確 URI。
     */
    public function testVoucherBaseUriReturnsSandboxHost(): void
    {
        $context = new ApiContext(true);

        $this->assertEquals('https://testservice.12cm.com.tw', $context->voucherBaseUri());
    }

    /**
     * 測試 setSandbox 動態切換 voucherBaseUri。
     */
    public function testVoucherBaseUriSwitchesWithSandboxToggle(): void
    {
        $context = new ApiContext(false);

        $this->assertEquals('https://service.12cm.com.tw', $context->voucherBaseUri());

        $context->setSandbox(true);

        $this->assertEquals('https://testservice.12cm.com.tw', $context->voucherBaseUri());
    }

    /**
     * 測試 rewardsCardBaseUri 回傳正確 URI。
     */
    public function testRewardsCardBaseUriReturnsCorrectHost(): void
    {
        $context = new ApiContext();

        $this->assertEquals('https://stagevip-api.12cm.com.tw', $context->rewardsCardBaseUri());
    }

    /**
     * 測試 timeout 預設值為 10.0 秒。
     */
    public function testTimeoutDefaultsToTenSeconds(): void
    {
        $context = new ApiContext();

        $this->assertEquals(10.0, $context->timeout());
    }

    /**
     * 測試 setTimeout 方法設定逾時。
     */
    public function testSetTimeoutSetsTimeout(): void
    {
        $context = new ApiContext();

        $result = $context->setTimeout(30.0);

        $this->assertEquals(30.0, $context->timeout());
        $this->assertSame($context, $result); // 測試 fluent interface
    }

    /**
     * 測試建構子可自訂所有參數。
     */
    public function testConstructorWithCustomParameters(): void
    {
        $context = new ApiContext(
            true,
            'https://custom-prod.example.com',
            'https://custom-sandbox.example.com',
            'https://custom-rewards.example.com',
            25.0
        );

        $this->assertTrue($context->useSandbox());
        $this->assertEquals('https://custom-sandbox.example.com', $context->voucherBaseUri());
        $this->assertEquals('https://custom-rewards.example.com', $context->rewardsCardBaseUri());
        $this->assertEquals(25.0, $context->timeout());

        // 切換到 production 確認自訂 host
        $context->setSandbox(false);
        $this->assertEquals('https://custom-prod.example.com', $context->voucherBaseUri());
    }
}

