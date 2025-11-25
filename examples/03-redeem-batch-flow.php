<?php

/**
 * Echoss VIP Voucher SDK - 核銷批次完整流程範例
 *
 * 此範例說明票券核銷的完整流程：
 * 1. 建立核銷批次
 * 2. 查詢核銷批次狀態
 * 3. 凍結批次
 * 4. 更新批次
 * 5. 執行核銷
 * 6. 核銷沖正（取消）
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CHYP\Partner\Echooss\Voucher\Core;
use CHYP\Partner\Echooss\Voucher\Type\Request\CreateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\QueryRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\QueryRedeemBatchDetail;
use CHYP\Partner\Echooss\Voucher\Type\Request\FreezeRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\UpdateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\ExecuteRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\ReverseRedeem;
use CHYP\Partner\Echooss\Voucher\Type\Request\Redeem;
use CHYP\Partner\Echooss\Voucher\Exception\ResponseTypeException;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;

// 初始化 SDK（Sandbox 環境）
$core = (new Core(true))->setToken('your-token-here');

// =============================================================================
// 步驟 1：建立核銷批次 (createRedeemBatch)
// =============================================================================

echo "=== 步驟 1：建立核銷批次 ===\n";

try {
    $createRequest = new CreateRedeemBatch();

    // 設定會員資訊
    $createRequest->phoneNumber = '0912345678';

    // 設定門市資訊
    $createRequest->storeOpenId = 'STORE001';
    $createRequest->posMacUid = 'POS001';

    // 設定要核銷的票券清單
    // Redeem 建構參數: redeemType (1: 優惠券, 2: 商品券), redeemId (票券 Hash ID), redeemQuantity (數量)
    $createRequest->batchList = [
        (new Redeem(1, 'voucher-hash-id-001', 1))->toArray(),
        (new Redeem(1, 'voucher-hash-id-002', 2))->toArray(),
    ];

    $response = $core->voucher('createRedeemBatch', $createRequest);

    echo "批次建立成功！\n";
    echo "批次 Token: " . $response->batchToken . "\n";
    echo "批次 UUID: " . $response->batchUuid . "\n";

    // 保存批次資訊供後續步驟使用
    $batchToken = $response->batchToken;
    $batchUuid = $response->batchUuid;
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";

// =============================================================================
// 步驟 2：查詢核銷批次狀態 (queryRedeemBatch)
// =============================================================================

echo "=== 步驟 2：查詢核銷批次狀態 ===\n";

try {
    $queryRequest = new QueryRedeemBatch();
    $queryRequest->batchToken = $batchToken;
    $queryRequest->storeOpenId = 'STORE001';
    $queryRequest->posMacUid = 'POS001';

    $response = $core->voucher('queryRedeemBatch', $queryRequest);

    echo "查詢成功！\n";
    echo "批次狀態: " . ($response->success ? '有效' : '無效') . "\n";
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
}

echo "\n";

// =============================================================================
// 步驟 2.1：查詢核銷批次明細 (queryRedeemBatchDetail)
// =============================================================================

echo "=== 步驟 2.1：查詢核銷批次明細 ===\n";

try {
    $detailRequest = new QueryRedeemBatchDetail();
    $detailRequest->batchUuid = $batchUuid;
    $detailRequest->storeOpenId = 'STORE001';
    $detailRequest->posMacUid = 'POS001';

    $response = $core->voucher('queryRedeemBatchDetail', $detailRequest);

    echo "明細查詢成功！\n";

    // 顯示批次內的票券明細
    if (isset($response->data) && is_array($response->data)) {
        foreach ($response->data as $detail) {
            echo "- 票券: " . ($detail->voucherName ?? 'N/A') . "\n";
            echo "  數量: " . ($detail->quantity ?? 'N/A') . "\n";
        }
    }
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
}

echo "\n";

// =============================================================================
// 步驟 3：凍結批次 (freezeRedeemBatch)
// =============================================================================

echo "=== 步驟 3：凍結批次 ===\n";

try {
    $freezeRequest = new FreezeRedeemBatch();
    $freezeRequest->batchUuid = $batchUuid;
    $freezeRequest->storeOpenId = 'STORE001';
    $freezeRequest->posMacUid = 'POS001';

    // 凍結時間（分鐘），必須在 1-60 之間
    $freezeRequest->freezeMins = 15;

    $response = $core->voucher('freezeRedeemBatch', $freezeRequest);

    echo "批次凍結成功！凍結 15 分鐘\n";
} catch (RequestTypeException $e) {
    // freezeMins 超出範圍時會拋出此例外
    echo "請求參數錯誤: " . $e->getMessage() . "\n";
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
}

echo "\n";

// =============================================================================
// 步驟 4：更新批次內容 (updateRedeemBatch)
// =============================================================================

echo "=== 步驟 4：更新批次內容 ===\n";

try {
    $updateRequest = new UpdateRedeemBatch();
    $updateRequest->batchUuid = $batchUuid;
    $updateRequest->storeOpenId = 'STORE001';
    $updateRequest->posMacUid = 'POS001';

    // 更新後的票券清單
    $updateRequest->batchList = [
        (new Redeem(1, 'voucher-hash-id-001', 1))->toArray(),
        // 移除第二張票券，或修改數量
    ];

    $response = $core->voucher('updateRedeemBatch', $updateRequest);

    echo "批次更新成功！\n";
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
}

echo "\n";

// =============================================================================
// 步驟 5：執行核銷 (executeRedeemBatch)
// =============================================================================

echo "=== 步驟 5：執行核銷 ===\n";

try {
    $executeRequest = new ExecuteRedeemBatch();
    $executeRequest->batchUuid = $batchUuid;
    $executeRequest->storeOpenId = 'STORE001';
    $executeRequest->posMacUid = 'POS001';

    $response = $core->voucher('executeRedeemBatch', $executeRequest);

    echo "核銷執行成功！\n";
    echo "交易結果: " . ($response->success ? '成功' : '失敗') . "\n";
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
}

echo "\n";

// =============================================================================
// 步驟 6：核銷沖正 (reverseRedeem) - 用於取消核銷
// =============================================================================

echo "=== 步驟 6：核銷沖正（取消核銷） ===\n";

try {
    $reverseRequest = new ReverseRedeem();

    // 可使用 phoneNumber 或 lineId 指定會員
    $reverseRequest->phoneNumber = '0912345678';

    // 票券類型：1 = 優惠券, 2 = 商品券
    $reverseRequest->type = 1;

    // 要沖正的票券 Hash ID
    $reverseRequest->voucherHashId = 'voucher-hash-id-001';

    // 沖正數量
    $reverseRequest->deductCount = 1;

    $response = $core->voucher('reverseRedeem', $reverseRequest);

    echo "核銷沖正成功！\n";
} catch (ResponseTypeException $e) {
    echo "API 錯誤: " . $e->getMessage() . "\n";
}

echo "\n=== 核銷批次流程範例結束 ===\n";

// =============================================================================
// 流程說明
// =============================================================================

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                    核銷流程說明                              ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║ 1. createRedeemBatch  - 建立核銷批次，取得 batchToken/UUID   ║\n";
echo "║ 2. queryRedeemBatch   - 查詢批次狀態                         ║\n";
echo "║ 3. freezeRedeemBatch  - 凍結批次（鎖定票券 1-60 分鐘）       ║\n";
echo "║ 4. updateRedeemBatch  - 更新批次內容（可選）                 ║\n";
echo "║ 5. executeRedeemBatch - 確認執行核銷                         ║\n";
echo "║ 6. reverseRedeem      - 核銷沖正（取消已核銷票券）           ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";

