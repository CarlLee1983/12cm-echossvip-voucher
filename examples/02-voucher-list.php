<?php

/**
 * Echoss VIP Voucher SDK - 票券清單查詢範例
 *
 * 此範例說明如何查詢會員的票券清單。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CHYP\Partner\Echooss\Voucher\Core;
use CHYP\Partner\Echooss\Voucher\Type\Request\VoucherList;
use CHYP\Partner\Echooss\Voucher\Exception\ResponseTypeException;

// 初始化 SDK（Sandbox 環境）
$core = (new Core(true))->setToken('your-token-here');

// =============================================================================
// 範例 1：使用手機號碼查詢票券清單
// =============================================================================

echo "=== 範例 1：使用手機號碼查詢 ===\n";

try {
    // 建立請求物件
    $request = new VoucherList();
    $request->phoneNumber = '0912345678';

    // 執行 API 呼叫
    $response = $core->voucher('voucherList', $request);

    // 處理回應
    echo "查詢成功！\n";
    echo "票券數量: " . count($response->data) . "\n";

    // 遍歷票券資料
    foreach ($response->data as $voucher) {
        echo "---\n";
        echo "票券名稱: " . ($voucher->voucherName ?? 'N/A') . "\n";
        echo "票券 ID: " . ($voucher->voucherHashId ?? 'N/A') . "\n";
        echo "剩餘數量: " . ($voucher->remainQuantity ?? 'N/A') . "\n";
        echo "到期日: " . ($voucher->expireDate ?? 'N/A') . "\n";
    }
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
    echo "錯誤碼: " . $e->getCode() . "\n";
}

echo "\n";

// =============================================================================
// 範例 2：使用 LINE ID 查詢票券清單
// =============================================================================

echo "=== 範例 2：使用 LINE ID 查詢 ===\n";

try {
    $request = new VoucherList();
    $request->lineId = 'U1234567890abcdef';

    $response = $core->voucher('voucherList', $request);

    echo "查詢成功！票券數量: " . count($response->data) . "\n";
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
}

echo "\n";

// =============================================================================
// 範例 3：使用 UseCase 類別直接呼叫
// =============================================================================

echo "=== 範例 3：使用 UseCase 類別 ===\n";

use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\VoucherListUseCase;

try {
    $request = new VoucherList();
    $request->phoneNumber = '0912345678';

    // 直接建立 UseCase 物件
    $useCase = new VoucherListUseCase($request);

    // 傳入 UseCase 物件執行
    $response = $core->voucher($useCase);

    echo "使用 UseCase 查詢成功！\n";
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
}

echo "\n";

// =============================================================================
// 範例 4：回應資料轉換為陣列
// =============================================================================

echo "=== 範例 4：回應轉陣列 ===\n";

try {
    $request = new VoucherList();
    $request->phoneNumber = '0912345678';

    $response = $core->voucher('voucherList', $request);

    // 使用 toArray() 方法將回應轉換為陣列
    $arrayData = $response->toArray();

    echo "轉換為陣列格式:\n";
    print_r($arrayData);
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
}

echo "\n";

// =============================================================================
// 範例 5：使用舊版 API（已棄用，僅供遷移參考）
// =============================================================================

echo "=== 範例 5：舊版 API（已棄用） ===\n";

try {
    $request = new VoucherList();
    $request->phoneNumber = '0912345678';

    // @deprecated 使用 voucherLegacy() 取得舊版 Response 格式
    $legacyResponse = $core->voucherLegacy('voucherList', $request);

    // 呼叫 format() 取得舊版格式資料
    $formatted = $legacyResponse->format();

    echo "舊版格式回應:\n";
    print_r($formatted);
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
}

echo "\n=== 票券清單查詢範例結束 ===\n";

