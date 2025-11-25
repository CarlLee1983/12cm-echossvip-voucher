<?php

namespace CHYP\Partner\Echooss\Voucher\Laravel\Facades;

use CHYP\Partner\Echooss\Voucher\Core;
use CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface;
use CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface;
use Illuminate\Support\Facades\Facade;

/**
 * Echoss VIP Voucher Facade.
 *
 * 提供靜態方法存取 Echoss Voucher SDK 的功能。
 *
 * @method static bool useSandbox() 判斷是否使用沙盒環境
 * @method static string getToken() 取得 API Token
 * @method static Core setToken(string $token) 設定 API Token
 * @method static ResponseInterface voucher(string $action, RequestInterface $param = null) 執行 Voucher API
 * @method static ResponseInterface rewardsCard(string $action, array $param) 執行 Rewards Card API
 * @method static \CHYP\Partner\Echooss\Voucher\Type\Response voucherLegacy(string $action, RequestInterface $param = null) 執行 Voucher API（舊版相容）
 * @method static \CHYP\Partner\Echooss\Voucher\Type\Response rewardsCardLegacy(string $action, array $param) 執行 Rewards Card API（舊版相容）
 * @method static \Psr\Http\Message\ResponseInterface request(string $method, string $path, array $content = []) 底層 HTTP 請求
 * @method static array deepDeconstruction(mixed $row) 將 Request/Response 物件解構為陣列
 * @method static \CHYP\Partner\Echooss\Voucher\Application\Factory\VoucherUseCaseFactory getVoucherUseCaseFactory() 取得 Voucher UseCase 工廠
 * @method static \CHYP\Partner\Echooss\Voucher\Application\Factory\RewardsCardUseCaseFactory getRewardsCardUseCaseFactory() 取得 RewardsCard UseCase 工廠
 *
 * @see \CHYP\Partner\Echooss\Voucher\Core
 */
class EchossVoucher extends Facade
{
    /**
     * 取得 Facade 對應的服務容器綁定名稱。
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Core::class;
    }
}

