# Echoss VIP Voucher SDK 範例程式

此資料夾包含 Echoss VIP Voucher SDK 的使用範例，幫助開發者快速了解如何整合票券與獎勵卡相關 API。

## 檔案說明

| 檔案 | 說明 |
|------|------|
| `01-basic-setup.php` | SDK 基本初始化與設定 |
| `02-voucher-list.php` | 查詢會員票券清單 |
| `03-redeem-batch-flow.php` | 票券核銷完整流程 |
| `04-rewards-card.php` | 獎勵卡點數累積與扣除 |
| `05-error-handling.php` | 錯誤處理與例外捕捉 |

## 執行前準備

### 1. 安裝相依套件

```bash
composer install
```

### 2. 設定環境變數

建議建立 `.env` 檔案來管理 API Token：

```bash
# .env
VOUCHER_TOKEN=your-voucher-api-token
REWARDS_CARD_TOKEN=your-rewards-card-api-token
```

### 3. 執行範例

```bash
php examples/01-basic-setup.php
php examples/02-voucher-list.php
# ... 以此類推
```

## 快速開始

### 初始化 SDK

```php
use CHYP\Partner\Echooss\Voucher\Core;

// Sandbox 環境
$core = (new Core(true))->setToken('your-token');

// Production 環境
$core = (new Core(false))->setToken('your-token');
```

### 查詢票券清單

```php
use CHYP\Partner\Echooss\Voucher\Type\Request\VoucherList;

$request = new VoucherList();
$request->phoneNumber = '0912345678';

$response = $core->voucher('voucherList', $request);

foreach ($response->data as $voucher) {
    echo $voucher->voucherName . "\n";
}
```

### 核銷票券

```php
use CHYP\Partner\Echooss\Voucher\Type\Request\CreateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\Redeem;
use CHYP\Partner\Echooss\Voucher\Type\Request\ExecuteRedeemBatch;

// 步驟 1：建立核銷批次
$createRequest = new CreateRedeemBatch();
$createRequest->phoneNumber = '0912345678';
$createRequest->storeOpenId = 'STORE001';
$createRequest->posMacUid = 'POS001';
$createRequest->batchList = [
    (new Redeem(1, 'voucher-hash-id', 1))->toArray(),
];

$response = $core->voucher('createRedeemBatch', $createRequest);
$batchUuid = $response->batchUuid;

// 步驟 2：執行核銷
$executeRequest = new ExecuteRedeemBatch();
$executeRequest->batchUuid = $batchUuid;
$executeRequest->storeOpenId = 'STORE001';
$executeRequest->posMacUid = 'POS001';

$core->voucher('executeRedeemBatch', $executeRequest);
```

### 累積點數

```php
use CHYP\Partner\Echooss\Voucher\Type\Request\AccumulatePoint;
use CHYP\Partner\Echooss\Voucher\Type\Request\AccumulatePointDetail;

$request = new AccumulatePoint();
$request->phoneNumber = '0912345678';
$request->amount = 1000;
$request->details = [
    new AccumulatePointDetail('商品名稱', 500, 2),
];

// 注意：rewardsCard 方法接受陣列參數
$response = $core->rewardsCard('accumulatePoint', [$request]);
```

### 扣除點數

```php
use CHYP\Partner\Echooss\Voucher\Type\Request\DepletePoint;

$request = new DepletePoint();
$request->phoneNumber = '0912345678';
$request->point = 100;

$response = $core->rewardsCard('depletePoint', [$request]);
```

## API 功能總覽

### Voucher API（票券）

| Action | 說明 | Request 類別 |
|--------|------|-------------|
| `voucherList` | 查詢票券清單 | `VoucherList` |
| `createRedeemBatch` | 建立核銷批次 | `CreateRedeemBatch` |
| `queryRedeemBatch` | 查詢核銷批次 | `QueryRedeemBatch` |
| `queryRedeemBatchDetail` | 查詢批次明細 | `QueryRedeemBatchDetail` |
| `freezeRedeemBatch` | 凍結批次 | `FreezeRedeemBatch` |
| `updateRedeemBatch` | 更新批次 | `UpdateRedeemBatch` |
| `executeRedeemBatch` | 執行核銷 | `ExecuteRedeemBatch` |
| `reverseRedeem` | 核銷沖正 | `ReverseRedeem` |

### RewardsCard API（獎勵卡）

| Action | 說明 | Request 類別 |
|--------|------|-------------|
| `accumulatePoint` | 累積點數 | `AccumulatePoint` |
| `depletePoint` | 扣除點數 | `DepletePoint` |

## 錯誤處理

SDK 定義了兩種主要的例外類別：

### RequestTypeException

請求參數驗證失敗時拋出：

```php
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;

try {
    $request = new FreezeRedeemBatch();
    $request->freezeMins = 120; // 超出 1-60 範圍
} catch (RequestTypeException $e) {
    echo "參數錯誤: " . $e->getMessage();
}
```

### ResponseTypeException

API 回傳錯誤時拋出：

```php
use CHYP\Partner\Echooss\Voucher\Exception\ResponseTypeException;

try {
    $response = $core->voucher('voucherList', $request);
} catch (ResponseTypeException $e) {
    echo "HTTP 狀態碼: " . $e->getCode();
    echo "錯誤訊息: " . $e->getMessage();
}
```

### 常見 HTTP 狀態碼

| 狀態碼 | 說明 |
|--------|------|
| 401 | Token 無效或過期 |
| 422 | 資料驗證失敗（如 user not found） |
| 500 | 伺服器內部錯誤 |

## 進階用法

### 使用 UseCase 類別

```php
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\VoucherListUseCase;

$useCase = new VoucherListUseCase($request);
$response = $core->voucher($useCase);
```

### 自訂 Gateway（佇列/事件整合）

```php
use CHYP\Partner\Echooss\Voucher\Application\Gateway\VoucherGatewayInterface;

class QueueVoucherGateway implements VoucherGatewayInterface
{
    public function post(string $path, array $payload): array
    {
        // 將請求丟入佇列
        $this->queue->dispatch(new VoucherJob($path, $payload));
        return ['queued' => true];
    }
}
```

## 注意事項

1. **環境區分**：Sandbox 與 Production 使用不同的 API 端點，請確認初始化時的 `isSandBox` 參數。
2. **Token 管理**：請妥善保管 API Token，建議使用環境變數而非硬編碼。
3. **批次操作**：`rewardsCard()` 方法接受陣列參數，可同時處理多筆會員資料。
4. **核銷流程**：完整核銷流程包含建立批次、凍結、執行等步驟，請參考 `03-redeem-batch-flow.php`。

## 相關資源

- [專案 README](../README.md) - SDK 完整文件
- [PHPUnit 測試](../tests/) - 更多 API 使用範例

