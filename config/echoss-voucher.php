<?php

/*
|--------------------------------------------------------------------------
| Echoss VIP Voucher SDK 配置檔案
|--------------------------------------------------------------------------
|
| 此配置檔案用於 Laravel 框架整合。
| 請透過 `php artisan vendor:publish --tag=echoss-voucher-config` 發佈至專案中。
|
*/

// 確保 env() 函式存在（非 Laravel 環境時提供相容性）
if (!function_exists('env')) {
    /**
     * 取得環境變數值（非 Laravel 環境的備用實作）。
     *
     * @param string $key     環境變數名稱
     * @param mixed  $default 預設值
     *
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
            case 'empty':
            case '(empty)':
                return '';
        }

        return $value;
    }
}

return [

    /*
    |--------------------------------------------------------------------------
    | Sandbox 模式
    |--------------------------------------------------------------------------
    |
    | 設定為 true 時，SDK 會使用沙盒環境進行 API 呼叫。
    | 建議在開發與測試環境中啟用，正式環境請設為 false。
    |
    */
    'sandbox' => env('ECHOSS_SANDBOX', false),

    /*
    |--------------------------------------------------------------------------
    | API Token
    |--------------------------------------------------------------------------
    |
    | Echoss VIP API 的認證 Token。
    | 建議透過環境變數設定，避免將敏感資訊寫入版本控制。
    |
    */
    'token' => env('ECHOSS_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | HTTP Timeout
    |--------------------------------------------------------------------------
    |
    | API 請求的逾時時間（秒）。
    | 預設為 10 秒，可依需求調整。
    |
    */
    'timeout' => env('ECHOSS_TIMEOUT', 10.0),

    /*
    |--------------------------------------------------------------------------
    | API Hosts
    |--------------------------------------------------------------------------
    |
    | 各服務端點的主機位址。
    | 通常不需要修改，除非有特殊的部署需求。
    |
    */
    'hosts' => [
        // Voucher 正式環境
        'voucher_production' => env('ECHOSS_VOUCHER_PROD_HOST', 'https://service.12cm.com.tw'),

        // Voucher 沙盒環境
        'voucher_sandbox' => env('ECHOSS_VOUCHER_SANDBOX_HOST', 'https://testservice.12cm.com.tw'),

        // Rewards Card 服務端點
        'rewards_card' => env('ECHOSS_REWARDS_CARD_HOST', 'https://stagevip-api.12cm.com.tw'),
    ],

];

