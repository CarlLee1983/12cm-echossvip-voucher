# Echoss VIP Voucher SDK

此版本針對原有 SDK 進行分層重構，將 HTTP Gateway、Use Case、DTO 映射與 Facade（`Core`）清楚拆離，方便未來接入事件、佇列或替換傳輸層。以下為最新使用方式與相容指引。

## 安裝

```bash
composer require chyp-partner-api-sdk/echoss-voucher
```

## 快速開始

```php
use CHYP\Partner\Echooss\Voucher\Core;
use CHYP\Partner\Echooss\Voucher\Type\Request\VoucherList;

$core = (new Core(true))->setToken('your-token');

$request = new VoucherList();
$request->phoneNumber = '0912xxxxxx';

$response = $core->voucher('voucherList', $request); // 回傳 ResponseInterface
$list = $response->data; // 為 Voucher 物件陣列
```

### 直接使用 Use Case 類別

```php
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\VoucherListUseCase;

$useCase = new VoucherListUseCase($request);
$response = $core->voucher($useCase);
```

Rewards Card 的使用方式相同，差別在於傳入陣列（可同時操作多筆）並使用對應 Use Case（`AccumulatePointUseCase`、`DepletePointUseCase`）。

## Response Interface

重構後 `Core::voucher()` 與 `Core::rewardsCard()` 會直接回傳對應的 `ResponseInterface` 具體類別，例如：

```php
$response = $core->voucher('createRedeemBatch', $request);
$response->success;      // bool
$response->batchToken;   // string
```

`ResponseHydrator` 會自動將 API 回傳的 snake_case 欄位轉為 camelCase 屬性，並保有 `toArray()` 方法便於序列化。

## 舊版相容模式

若現有專案仍依賴 `CHYP\Partner\Echooss\Voucher\Type\Response` 的 `format()` API，可透過以下 helper 取得相同行為：

```php
/** @var \CHYP\Partner\Echooss\Voucher\Core $core */
$legacy = $core->voucherLegacy('voucherList', $request);
$response = $legacy->format(); // 與舊版相同
```

Rewards Card 對應 `rewardsCardLegacy()`。這兩個方法已標記為 `@deprecated`，建議逐步移轉至新的 `ResponseInterface`。

## DI 與擴充

- `ApiContext` 集中 token、host、timeout 與 sandbox 設定，必要時可覆寫建構參數或再包一層設定檔注入。
- `VoucherGatewayInterface`、`RewardsCardGatewayInterface` 可由自訂實作替換，例如：

```php
class QueueVoucherGateway implements VoucherGatewayInterface
{
    public function __construct(private MessageBus $bus) {}

    public function post(string $path, array $payload): array
    {
        $this->bus->dispatch(new VoucherJob($path, $payload));
        return ['queued' => true];
    }
}

$core = new Core(
    isSandBox: true,
    voucherService: new VoucherService(
        new QueueVoucherGateway($bus),
        new RequestAssembler(),
        new ResponseHydrator(new ResponseFactory())
    )
);
```

- 透過替換 Gateway，即可導入佇列／事件流，或於 Service 層注入 decorator 發佈 `VoucherRedeemed` 等事件，無須修改 domain DTO。

## 事件／佇列規劃建議

1. **Decorator Pattern**：以 `class EventfulVoucherService implements VoucherGatewayInterface` 包裝既有 gateway，於 `post()` 成功後觸發事件，失敗時統一記錄。
2. **Queue Gateway**：實作 `VoucherGatewayInterface` 將 payload 丟入訊息佇列，Worker 端再重用同一組 `RequestAssembler`/`ResponseHydrator` 進行實際 HTTP 呼叫。
3. **Domain Events**：在 Application Service (`VoucherService::handle`) 外層建立事件發布器，於取得 `ResponseInterface` 後轉換為 `VoucherRedeemed`, `RedeemBatchCreated` 等內部事件，供後續模組訂閱。

## Deprecation 指引

- `Core::voucherLegacy()` / `Core::rewardsCardLegacy()`：僅供舊專案過渡，未來會移除。
- `CHYP\Partner\Echooss\Voucher\Voucher`、`RewardsCard` 類別仍保留，但建議直接透過 `Core` Facade 操作並逐步移除對舊類別的依賴。

若在升級過程中需要更多範例或協助，可依自身的 DI 容器或框架（Laravel、Symfony 等）替換 `ApiContext` 與 Gateway 實作。透過目前的抽象層，即可在不影響 Domain DTO 的情況下，擴充事件、佇列或觀察者機制。***

