<?php

/**
 * Echoss VIP Voucher SDK - 錯誤處理範例
 *
 * 此範例說明如何正確處理 SDK 可能拋出的各種例外情況。
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CHYP\Partner\Echooss\Voucher\Core;
use CHYP\Partner\Echooss\Voucher\Type\Request\VoucherList;
use CHYP\Partner\Echooss\Voucher\Type\Request\FreezeRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Exception\ResponseTypeException;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;

$core = (new Core(true))->setToken('your-token-here');

// =============================================================================
// 範例 1：處理 ResponseTypeException（API 回應錯誤）
// =============================================================================

echo "=== 範例 1：ResponseTypeException 處理 ===\n";

try {
    $request = new VoucherList();
    $request->phoneNumber = '0912345678';

    $response = $core->voucher('voucherList', $request);
} catch (ResponseTypeException $e) {
    // 取得錯誤訊息（通常是 JSON 格式）
    $message = $e->getMessage();

    // 取得 HTTP 狀態碼
    $statusCode = $e->getCode();

    echo "API 錯誤發生！\n";
    echo "HTTP 狀態碼: " . $statusCode . "\n";
    echo "錯誤訊息: " . $message . "\n";

    // 嘗試解析 JSON 錯誤訊息
    $errorData = json_decode($message, true);
    if (json_last_error() === JSON_ERROR_NONE && isset($errorData['message'])) {
        echo "解析後的錯誤: " . $errorData['message'] . "\n";
    }
}

echo "\n";

// =============================================================================
// 範例 2：處理 RequestTypeException（請求參數錯誤）
// =============================================================================

echo "=== 範例 2：RequestTypeException 處理 ===\n";

try {
    $request = new FreezeRedeemBatch();

    // freezeMins 必須在 1-60 之間，設定超過範圍的值會拋出例外
    $request->freezeMins = 120;
} catch (RequestTypeException $e) {
    echo "請求參數錯誤！\n";
    echo "錯誤訊息: " . $e->getMessage() . "\n";
}

echo "\n";

// =============================================================================
// 範例 3：根據 HTTP 狀態碼分類處理
// =============================================================================

echo "=== 範例 3：HTTP 狀態碼分類處理 ===\n";

try {
    // 模擬未設定 Token 的情況
    $coreNoAuth = (new Core(true))->setToken('');

    $request = new VoucherList();
    $request->phoneNumber = '0912345678';

    $response = $coreNoAuth->voucher('voucherList', $request);
} catch (ResponseTypeException $e) {
    $statusCode = $e->getCode();

    switch ($statusCode) {
        case 400:
            echo "錯誤請求 (400): 請求格式或參數有誤\n";
            break;

        case 401:
            echo "未授權 (401): Token 無效或已過期，請重新取得 Token\n";
            break;

        case 403:
            echo "禁止存取 (403): 沒有權限執行此操作\n";
            break;

        case 404:
            echo "資源不存在 (404): 請確認 API 路徑或資源 ID\n";
            break;

        case 422:
            echo "驗證失敗 (422): 請求資料驗證未通過\n";
            // 嘗試取得詳細錯誤
            $errorData = json_decode($e->getMessage(), true);
            if (isset($errorData['errors'])) {
                echo "詳細錯誤: " . $errorData['errors'] . "\n";
            }
            break;

        case 429:
            echo "請求過於頻繁 (429): 請稍後再試\n";
            break;

        case 500:
            echo "伺服器錯誤 (500): API 伺服器發生問題，請聯繫技術支援\n";
            break;

        default:
            echo "未知錯誤 ($statusCode): " . $e->getMessage() . "\n";
    }
}

echo "\n";

// =============================================================================
// 範例 4：完整的錯誤處理流程
// =============================================================================

echo "=== 範例 4：完整錯誤處理流程 ===\n";

/**
 * 封裝 API 呼叫的錯誤處理
 *
 * @param callable $apiCall API 呼叫函數
 *
 * @return array 包含 success 狀態和資料或錯誤的陣列
 */
function safeApiCall(callable $apiCall): array
{
    try {
        $response = $apiCall();

        return [
            'success' => true,
            'data' => $response->toArray(),
        ];
    } catch (RequestTypeException $e) {
        return [
            'success' => false,
            'error_type' => 'request_validation',
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
        ];
    } catch (ResponseTypeException $e) {
        $errorData = json_decode($e->getMessage(), true);

        return [
            'success' => false,
            'error_type' => 'api_response',
            'message' => $errorData['message'] ?? $e->getMessage(),
            'code' => $e->getCode(),
            'raw_error' => $e->getMessage(),
        ];
    } catch (\GuzzleHttp\Exception\ConnectException $e) {
        return [
            'success' => false,
            'error_type' => 'network',
            'message' => '網路連線失敗，請檢查網路狀態',
            'code' => 0,
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'error_type' => 'unknown',
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
        ];
    }
}

// 使用封裝函數
$result = safeApiCall(function () use ($core) {
    $request = new VoucherList();
    $request->phoneNumber = '0912345678';

    return $core->voucher('voucherList', $request);
});

if ($result['success']) {
    echo "API 呼叫成功！\n";
    print_r($result['data']);
} else {
    echo "API 呼叫失敗\n";
    echo "錯誤類型: " . $result['error_type'] . "\n";
    echo "錯誤訊息: " . $result['message'] . "\n";
    echo "錯誤碼: " . $result['code'] . "\n";
}

echo "\n";

// =============================================================================
// 範例 5：重試機制
// =============================================================================

echo "=== 範例 5：重試機制 ===\n";

/**
 * 帶重試機制的 API 呼叫
 *
 * @param callable $apiCall   API 呼叫函數
 * @param int      $maxRetries 最大重試次數
 * @param int      $delayMs    重試間隔（毫秒）
 *
 * @return mixed API 回應
 *
 * @throws ResponseTypeException 當所有重試都失敗時
 */
function apiCallWithRetry(callable $apiCall, int $maxRetries = 3, int $delayMs = 1000)
{
    $lastException = null;

    for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
        try {
            return $apiCall();
        } catch (ResponseTypeException $e) {
            $lastException = $e;

            // 只對可重試的錯誤進行重試（如 429, 500, 502, 503, 504）
            $retryableCodes = [429, 500, 502, 503, 504];
            if (!in_array($e->getCode(), $retryableCodes)) {
                throw $e;
            }

            echo "第 {$attempt} 次嘗試失敗，{$delayMs}ms 後重試...\n";

            if ($attempt < $maxRetries) {
                usleep($delayMs * 1000);
            }
        }
    }

    throw $lastException;
}

try {
    $response = apiCallWithRetry(function () use ($core) {
        $request = new VoucherList();
        $request->phoneNumber = '0912345678';

        return $core->voucher('voucherList', $request);
    }, 3, 500);

    echo "API 呼叫成功（可能經過重試）\n";
} catch (ResponseTypeException $e) {
    echo "所有重試都失敗: " . $e->getMessage() . "\n";
}

echo "\n";

// =============================================================================
// 範例 6：日誌記錄
// =============================================================================

echo "=== 範例 6：錯誤日誌記錄 ===\n";

/**
 * 記錄 API 錯誤到日誌
 *
 * @param \Exception $e       例外物件
 * @param string     $context 呼叫上下文
 */
function logApiError(\Exception $e, string $context): void
{
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'context' => $context,
        'error_type' => get_class($e),
        'code' => $e->getCode(),
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ];

    // 實際應用中可寫入檔案或送到日誌服務
    echo "=== 錯誤日誌 ===\n";
    echo json_encode($logEntry, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}

try {
    $request = new VoucherList();
    $request->phoneNumber = '0912345678';

    $response = $core->voucher('voucherList', $request);
} catch (ResponseTypeException $e) {
    logApiError($e, 'VoucherList API 呼叫');
}

echo "\n=== 錯誤處理範例結束 ===\n";

// =============================================================================
// 例外類型總覽
// =============================================================================

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║                      例外類型總覽                                ║\n";
echo "╠══════════════════════════════════════════════════════════════════╣\n";
echo "║ RequestTypeException                                             ║\n";
echo "║   - 發生時機：請求參數驗證失敗（如 freezeMins 超出範圍）         ║\n";
echo "║   - 處理方式：檢查並修正請求參數                                 ║\n";
echo "║                                                                  ║\n";
echo "║ ResponseTypeException                                            ║\n";
echo "║   - 發生時機：API 回傳錯誤（HTTP 4xx, 5xx）                       ║\n";
echo "║   - getCode()：取得 HTTP 狀態碼                                  ║\n";
echo "║   - getMessage()：取得錯誤訊息（通常為 JSON 格式）               ║\n";
echo "║                                                                  ║\n";
echo "║ 常見 HTTP 狀態碼：                                               ║\n";
echo "║   - 401：Token 無效或過期                                        ║\n";
echo "║   - 422：資料驗證失敗（如 user not found）                       ║\n";
echo "║   - 500：伺服器內部錯誤                                          ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n";

