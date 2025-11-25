<?php

/**
 * Echoss VIP Voucher SDK - 基本設定範例
 *
 * 此範例說明如何初始化 SDK 並進行基本設定。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CHYP\Partner\Echooss\Voucher\Core;
use CHYP\Partner\Echooss\Voucher\Application\ApiContext;

// =============================================================================
// 範例 1：基本初始化（Sandbox 環境）
// =============================================================================

echo "=== 範例 1：Sandbox 環境初始化 ===\n";

// 建立 Core 實例，傳入 true 表示使用 Sandbox 環境
$core = new Core(true);

// 設定 API Token（Bearer Token）
$core->setToken('your-sandbox-token-here');

echo "Sandbox 模式: " . ($core->useSandbox() ? '是' : '否') . "\n";
echo "Token: " . $core->getToken() . "\n\n";

// =============================================================================
// 範例 2：Production 環境初始化
// =============================================================================

echo "=== 範例 2：Production 環境初始化 ===\n";

// 傳入 false 或不傳參數表示使用 Production 環境
$coreProd = new Core(false);
$coreProd->setToken('your-production-token-here');

echo "Sandbox 模式: " . ($coreProd->useSandbox() ? '是' : '否') . "\n\n";

// =============================================================================
// 範例 3：使用環境變數設定 Token
// =============================================================================

echo "=== 範例 3：使用環境變數 ===\n";

// 建議使用 .env 檔案管理敏感資訊
// 需先安裝 vlucas/phpdotenv: composer require vlucas/phpdotenv

/*
// .env 檔案內容範例：
// VOUCHER_TOKEN=your-voucher-api-token
// REWARDS_CARD_TOKEN=your-rewards-card-api-token

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$core = (new Core(true))->setToken($_ENV['VOUCHER_TOKEN']);
*/

echo "提示：建議將 Token 存放於 .env 檔案中，避免硬編碼敏感資訊\n\n";

// =============================================================================
// 範例 4：使用自訂 ApiContext
// =============================================================================

echo "=== 範例 4：自訂 ApiContext ===\n";

// 可透過 ApiContext 自訂更多設定（如 timeout）
$context = new ApiContext(
    isSandBox: true,
    token: 'your-token-here',
    timeout: 30  // 自訂 HTTP 請求超時秒數
);

$coreWithContext = new Core(
    isSandBox: true,
    context: $context
);

echo "已使用自訂 ApiContext 建立 Core 實例\n\n";

// =============================================================================
// 範例 5：鏈式呼叫
// =============================================================================

echo "=== 範例 5：鏈式呼叫 ===\n";

// setToken 回傳 self，支援鏈式呼叫
$core = (new Core(true))->setToken('your-token');

echo "Token 已設定: " . $core->getToken() . "\n\n";

echo "=== 基本設定範例結束 ===\n";

