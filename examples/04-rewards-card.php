<?php

/**
 * Echoss VIP Voucher SDK - 獎勵卡點數操作範例
 *
 * 此範例說明如何進行會員獎勵卡的點數累積與扣除。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CHYP\Partner\Echooss\Voucher\Core;
use CHYP\Partner\Echooss\Voucher\Type\Request\AccumulatePoint;
use CHYP\Partner\Echooss\Voucher\Type\Request\AccumulatePointDetail;
use CHYP\Partner\Echooss\Voucher\Type\Request\DepletePoint;
use CHYP\Partner\Echooss\Voucher\Exception\ResponseTypeException;

// 初始化 SDK（Sandbox 環境）
// 注意：獎勵卡 API 可能需要不同的 Token
$core = (new Core(true))->setToken('your-rewards-card-token-here');

// =============================================================================
// 範例 1：累積點數 (accumulatePoint)
// =============================================================================

echo "=== 範例 1：累積點數 ===\n";

try {
    // 建立累積點數請求
    $accumulateRequest = new AccumulatePoint();

    // 設定會員手機號碼
    $accumulateRequest->phoneNumber = '0912345678';

    // 設定消費金額（必填）
    $accumulateRequest->amount = 1000;

    // 設定購買明細（選填，可多筆）
    $accumulateRequest->details = [
        new AccumulatePointDetail('商品 A', 500, 1),   // 商品名稱、單價、數量
        new AccumulatePointDetail('商品 B', 250, 2),
    ];

    // 注意：rewardsCard 方法接受陣列參數，可同時處理多筆
    $response = $core->rewardsCard('accumulatePoint', [$accumulateRequest]);

    echo "點數累積成功！\n";
    echo "操作結果: " . ($response->success ? '成功' : '失敗') . "\n";

    // 顯示回應資料
    if (isset($response->data)) {
        print_r($response->toArray());
    }
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
    echo "錯誤碼: " . $e->getCode() . "\n";
}

echo "\n";

// =============================================================================
// 範例 2：批次累積點數（多筆會員）
// =============================================================================

echo "=== 範例 2：批次累積點數 ===\n";

try {
    // 會員 1
    $request1 = new AccumulatePoint();
    $request1->phoneNumber = '0912345678';
    $request1->amount = 500;
    $request1->details = [
        new AccumulatePointDetail('咖啡', 150, 2),
        new AccumulatePointDetail('蛋糕', 200, 1),
    ];

    // 會員 2
    $request2 = new AccumulatePoint();
    $request2->phoneNumber = '0923456789';
    $request2->amount = 300;
    $request2->details = [
        new AccumulatePointDetail('茶飲', 100, 3),
    ];

    // 一次送出多筆累積請求
    $response = $core->rewardsCard('accumulatePoint', [$request1, $request2]);

    echo "批次累積成功！\n";
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
}

echo "\n";

// =============================================================================
// 範例 3：扣除點數 (depletePoint)
// =============================================================================

echo "=== 範例 3：扣除點數 ===\n";

try {
    // 建立扣除點數請求
    $depleteRequest = new DepletePoint();

    // 設定會員手機號碼
    $depleteRequest->phoneNumber = '0912345678';

    // 設定要扣除的點數（折抵金額）
    $depleteRequest->point = 100;

    // 同樣使用陣列形式傳入
    $response = $core->rewardsCard('depletePoint', [$depleteRequest]);

    echo "點數扣除成功！\n";
    echo "扣除點數: 100\n";
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
    echo "錯誤碼: " . $e->getCode() . "\n";
}

echo "\n";

// =============================================================================
// 範例 4：批次扣除點數
// =============================================================================

echo "=== 範例 4：批次扣除點數 ===\n";

try {
    $deplete1 = new DepletePoint();
    $deplete1->phoneNumber = '0912345678';
    $deplete1->point = 50;

    $deplete2 = new DepletePoint();
    $deplete2->phoneNumber = '0923456789';
    $deplete2->point = 30;

    $response = $core->rewardsCard('depletePoint', [$deplete1, $deplete2]);

    echo "批次扣除成功！\n";
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
}

echo "\n";

// =============================================================================
// 範例 5：使用 UseCase 類別
// =============================================================================

echo "=== 範例 5：使用 UseCase 類別 ===\n";

use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\AccumulatePointUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\DepletePointUseCase;

try {
    // 建立請求資料
    $request = new AccumulatePoint();
    $request->phoneNumber = '0912345678';
    $request->amount = 800;
    $request->details = [
        new AccumulatePointDetail('套餐', 800, 1),
    ];

    // 直接建立 UseCase 物件
    $useCase = new AccumulatePointUseCase([$request]);

    // 傳入 UseCase 執行
    $response = $core->rewardsCard($useCase, []);

    echo "使用 UseCase 累積點數成功！\n";
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
}

echo "\n";

// =============================================================================
// 範例 6：使用舊版 API（已棄用）
// =============================================================================

echo "=== 範例 6：舊版 API（已棄用） ===\n";

try {
    $request = new AccumulatePoint();
    $request->phoneNumber = '0912345678';
    $request->amount = 200;

    // @deprecated 使用 rewardsCardLegacy() 取得舊版回應格式
    $legacyResponse = $core->rewardsCardLegacy('accumulatePoint', [$request]);

    // 呼叫 format() 取得舊版格式
    $formatted = $legacyResponse->format();

    echo "舊版格式回應:\n";
    print_r($formatted);
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
}

echo "\n=== 獎勵卡點數操作範例結束 ===\n";

// =============================================================================
// 功能說明
// =============================================================================

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                    獎勵卡 API 說明                           ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║ accumulatePoint - 消費累積點數                               ║\n";
echo "║   - phoneNumber: 會員手機號碼                                ║\n";
echo "║   - amount: 消費金額（必填）                                 ║\n";
echo "║   - details: 購買明細（選填，可多筆）                        ║\n";
echo "║                                                              ║\n";
echo "║ depletePoint - 折抵扣除點數                                  ║\n";
echo "║   - phoneNumber: 會員手機號碼                                ║\n";
echo "║   - point: 扣除點數（折抵金額）                              ║\n";
echo "║                                                              ║\n";
echo "║ 注意：rewardsCard() 方法接受陣列參數，可批次處理多筆         ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";

