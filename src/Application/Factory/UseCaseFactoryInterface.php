<?php

namespace CHYP\Partner\Echooss\Voucher\Application\Factory;

/**
 * UseCase 工廠介面。
 *
 * 定義 UseCase 建立的標準契約，用於將 action 字串轉換為對應的 UseCase 實例。
 */
interface UseCaseFactoryInterface
{
    /**
     * 根據 action 名稱建立對應的 UseCase 實例。
     *
     * @param string $action  動作名稱。
     * @param mixed  $payload 請求資料（可以是 RequestInterface 或 array）。
     *
     * @return mixed UseCase 實例。
     */
    public function create(string $action, $payload);

    /**
     * 檢查是否支援指定的 action。
     *
     * @param string $action 動作名稱。
     *
     * @return boolean
     */
    public function supports(string $action): bool;

    /**
     * 取得所有支援的 action 名稱。
     *
     * @return array<string>
     */
    public function supportedActions(): array;
}
