<?php

/**
 * 範例 06：Laravel 框架整合
 *
 * 此範例展示如何在 Laravel 框架中使用 Echoss VIP Voucher SDK。
 *
 * 前置條件：
 * 1. 已安裝 Laravel 8.0 以上版本
 * 2. 已透過 composer 安裝此 SDK
 * 3. 已發佈配置檔案並設定環境變數
 */

// =====================================================================
// 方法一：使用 Facade
// =====================================================================

use CHYP\Partner\Echooss\Voucher\Laravel\Facades\EchossVoucher;
use CHYP\Partner\Echooss\Voucher\Type\Request\VoucherList;
use CHYP\Partner\Echooss\Voucher\Type\Request\CreateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\Redeem;

/**
 * 範例：查詢 Voucher 列表
 */
function exampleVoucherList(): void
{
    $request = new VoucherList();
    $request->phoneNumber = '0912345678';

    // 使用 Facade 靜態方法呼叫
    $response = EchossVoucher::voucher('voucherList', $request);

    if ($response->success) {
        foreach ($response->data as $voucher) {
            echo "Voucher: {$voucher->voucherName} - 數量: {$voucher->quantity}\n";
        }
    }
}

/**
 * 範例：建立兌換批次
 */
function exampleCreateRedeemBatch(): void
{
    $request = new CreateRedeemBatch();
    $request->phoneNumber = '0912345678';
    $request->storeUid = 'STORE001';
    $request->storeName = '測試門市';
    $request->items = [
        (function () {
            $redeem = new Redeem();
            $redeem->saleId = 'SALE001';
            $redeem->voucherCode = 'VOUCHER001';
            $redeem->voucherQuantity = 1;
            return $redeem;
        })(),
    ];

    $response = EchossVoucher::voucher('createRedeemBatch', $request);

    if ($response->success) {
        echo "批次建立成功，Token: {$response->batchToken}\n";
    }
}

/**
 * 範例：累積點數 (Rewards Card)
 */
function exampleAccumulatePoint(): void
{
    $response = EchossVoucher::rewardsCard('accumulatePoint', [
        [
            'phone_number' => '0912345678',
            'order_id' => 'ORDER001',
            'amount' => 1000,
            'store_uid' => 'STORE001',
            'store_name' => '測試門市',
        ],
    ]);

    if ($response->success) {
        echo "點數累積成功\n";
    }
}

// =====================================================================
// 方法二：依賴注入 (推薦用於 Controller / Service)
// =====================================================================

use CHYP\Partner\Echooss\Voucher\Core;
use Illuminate\Http\JsonResponse;

/**
 * 範例 Controller：展示依賴注入的使用方式
 */
class VoucherController
{
    /**
     * 取得使用者的 Voucher 列表
     *
     * @param Core    $echoss  透過依賴注入取得 Core 實例
     * @param string  $phone   使用者電話號碼
     *
     * @return JsonResponse
     */
    public function getUserVouchers(Core $echoss, string $phone): JsonResponse
    {
        $request = new VoucherList();
        $request->phoneNumber = $phone;

        $response = $echoss->voucher('voucherList', $request);

        return response()->json([
            'success' => $response->success,
            'data' => $response->data,
        ]);
    }
}

// =====================================================================
// 方法三：透過 Service Container 解析
// =====================================================================

/**
 * 範例：從 Service Container 手動解析
 */
function exampleServiceContainer(): void
{
    // 方式 A：透過 app() helper
    $echoss = app(Core::class);

    // 方式 B：透過別名
    $echoss = app('echoss.voucher');

    // 方式 C：透過 resolve() helper
    $echoss = resolve(Core::class);

    // 使用實例
    $request = new VoucherList();
    $request->phoneNumber = '0912345678';

    $response = $echoss->voucher('voucherList', $request);
}

// =====================================================================
// 進階用法：動態切換 Token
// =====================================================================

/**
 * 範例：根據不同租戶動態設定 Token
 */
function exampleMultiTenant(): void
{
    // 取得租戶的 Token（假設從資料庫讀取）
    $tenantToken = 'tenant-specific-token';

    // 方式一：使用 Facade
    EchossVoucher::setToken($tenantToken);

    // 方式二：透過依賴注入後設定
    $echoss = app(Core::class);
    $echoss->setToken($tenantToken);

    // 後續的 API 呼叫都會使用新的 Token
    $request = new VoucherList();
    $request->phoneNumber = '0912345678';

    $response = EchossVoucher::voucher('voucherList', $request);
}

// =====================================================================
// 配置檔案範例 (config/echoss-voucher.php)
// =====================================================================

/*
return [
    // 是否使用沙盒環境
    'sandbox' => env('ECHOSS_SANDBOX', false),

    // API Token
    'token' => env('ECHOSS_TOKEN', ''),

    // HTTP 逾時時間（秒）
    'timeout' => env('ECHOSS_TIMEOUT', 10.0),

    // API 主機位址
    'hosts' => [
        'voucher_production' => env('ECHOSS_VOUCHER_PROD_HOST', 'https://service.12cm.com.tw'),
        'voucher_sandbox' => env('ECHOSS_VOUCHER_SANDBOX_HOST', 'https://testservice.12cm.com.tw'),
        'rewards_card' => env('ECHOSS_REWARDS_CARD_HOST', 'https://stagevip-api.12cm.com.tw'),
    ],
];
*/

// =====================================================================
// .env 檔案範例
// =====================================================================

/*
ECHOSS_SANDBOX=false
ECHOSS_TOKEN=your-api-token-here
ECHOSS_TIMEOUT=10.0
*/

