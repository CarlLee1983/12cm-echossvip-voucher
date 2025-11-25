<?php

namespace Tests\Laravel;

use CHYP\Partner\Echooss\Voucher\Application\ApiContext;
use CHYP\Partner\Echooss\Voucher\Application\Factory\RewardsCardUseCaseFactory;
use CHYP\Partner\Echooss\Voucher\Application\Factory\VoucherUseCaseFactory;
use CHYP\Partner\Echooss\Voucher\Application\Gateway\RewardsCardGatewayInterface;
use CHYP\Partner\Echooss\Voucher\Application\Gateway\VoucherGatewayInterface;
use CHYP\Partner\Echooss\Voucher\Application\Service\RewardsCardService;
use CHYP\Partner\Echooss\Voucher\Application\Service\VoucherService;
use CHYP\Partner\Echooss\Voucher\Core;
use PHPUnit\Framework\TestCase;

/**
 * Laravel 整合相關單元測試。
 *
 * 注意：這些測試不依賴完整的 Laravel 框架，
 * 主要用於驗證配置檔案和 Facade 類別的正確性。
 */
class EchossVoucherServiceProviderTest extends TestCase
{
    /**
     * 測試配置檔案路徑存在。
     */
    public function testConfigFileExists(): void
    {
        $configPath = dirname(__DIR__, 2) . '/config/echoss-voucher.php';

        $this->assertFileExists($configPath);
    }

    /**
     * 測試配置檔案結構正確。
     */
    public function testConfigFileHasCorrectStructure(): void
    {
        $configPath = dirname(__DIR__, 2) . '/config/echoss-voucher.php';
        $config = require $configPath;

        $this->assertIsArray($config);
        $this->assertArrayHasKey('sandbox', $config);
        $this->assertArrayHasKey('token', $config);
        $this->assertArrayHasKey('timeout', $config);
        $this->assertArrayHasKey('hosts', $config);

        $this->assertIsArray($config['hosts']);
        $this->assertArrayHasKey('voucher_production', $config['hosts']);
        $this->assertArrayHasKey('voucher_sandbox', $config['hosts']);
        $this->assertArrayHasKey('rewards_card', $config['hosts']);
    }

    /**
     * 測試配置檔案的預設值正確。
     */
    public function testConfigFileDefaultValues(): void
    {
        $configPath = dirname(__DIR__, 2) . '/config/echoss-voucher.php';
        $config = require $configPath;

        $this->assertFalse($config['sandbox']);
        $this->assertEmpty($config['token']);
        $this->assertEquals(10.0, $config['timeout']);
        $this->assertEquals('https://service.12cm.com.tw', $config['hosts']['voucher_production']);
        $this->assertEquals('https://testservice.12cm.com.tw', $config['hosts']['voucher_sandbox']);
        $this->assertEquals('https://stagevip-api.12cm.com.tw', $config['hosts']['rewards_card']);
    }

    /**
     * 測試 Service Provider 檔案存在。
     */
    public function testServiceProviderFileExists(): void
    {
        $providerPath = dirname(__DIR__, 2) . '/src/Laravel/EchossVoucherServiceProvider.php';

        $this->assertFileExists($providerPath);
    }

    /**
     * 測試 Facade 檔案存在。
     */
    public function testFacadeFileExists(): void
    {
        $facadePath = dirname(__DIR__, 2) . '/src/Laravel/Facades/EchossVoucher.php';

        $this->assertFileExists($facadePath);
    }

    /**
     * 測試 Service Provider 類別定義正確的 provides 方法返回值。
     *
     * 透過解析 PHP 檔案內容來驗證，不需要實際載入 Laravel 類別。
     */
    public function testServiceProviderProvidesMethod(): void
    {
        $providerPath = dirname(__DIR__, 2) . '/src/Laravel/EchossVoucherServiceProvider.php';
        $content = file_get_contents($providerPath);

        // 驗證 provides 方法存在並包含必要的服務
        $this->assertStringContainsString('public function provides()', $content);
        $this->assertStringContainsString(Core::class, $content);
        $this->assertStringContainsString("'echoss.voucher'", $content);
        $this->assertStringContainsString(ApiContext::class, $content);
        $this->assertStringContainsString(VoucherGatewayInterface::class, $content);
        $this->assertStringContainsString(RewardsCardGatewayInterface::class, $content);
        $this->assertStringContainsString(VoucherService::class, $content);
        $this->assertStringContainsString(RewardsCardService::class, $content);
        $this->assertStringContainsString(VoucherUseCaseFactory::class, $content);
        $this->assertStringContainsString(RewardsCardUseCaseFactory::class, $content);
    }

    /**
     * 測試 Facade 類別定義正確的 accessor。
     */
    public function testFacadeAccessor(): void
    {
        $facadePath = dirname(__DIR__, 2) . '/src/Laravel/Facades/EchossVoucher.php';
        $content = file_get_contents($facadePath);

        // 驗證 getFacadeAccessor 方法返回正確的類別
        $this->assertStringContainsString('protected static function getFacadeAccessor()', $content);
        $this->assertStringContainsString('return Core::class', $content);
    }

    /**
     * 測試 composer.json 包含 Laravel 自動發現配置。
     */
    public function testComposerJsonHasLaravelAutoDiscovery(): void
    {
        $composerPath = dirname(__DIR__, 2) . '/composer.json';
        $composer = json_decode(file_get_contents($composerPath), true);

        $this->assertArrayHasKey('extra', $composer);
        $this->assertArrayHasKey('laravel', $composer['extra']);
        $this->assertArrayHasKey('providers', $composer['extra']['laravel']);
        $this->assertArrayHasKey('aliases', $composer['extra']['laravel']);

        $this->assertContains(
            'CHYP\\Partner\\Echooss\\Voucher\\Laravel\\EchossVoucherServiceProvider',
            $composer['extra']['laravel']['providers']
        );

        $this->assertArrayHasKey('EchossVoucher', $composer['extra']['laravel']['aliases']);
        $this->assertEquals(
            'CHYP\\Partner\\Echooss\\Voucher\\Laravel\\Facades\\EchossVoucher',
            $composer['extra']['laravel']['aliases']['EchossVoucher']
        );
    }

    /**
     * 測試環境變數的備用 env() 函式運作正常。
     */
    public function testEnvFunctionFallback(): void
    {
        // 載入配置檔案會定義 env() 函式
        $configPath = dirname(__DIR__, 2) . '/config/echoss-voucher.php';
        require_once $configPath;

        // 驗證 env() 函式存在
        $this->assertTrue(function_exists('env'));

        // 測試預設值
        $this->assertEquals('default', env('NON_EXISTENT_VAR', 'default'));
        $this->assertNull(env('NON_EXISTENT_VAR'));
    }
}

