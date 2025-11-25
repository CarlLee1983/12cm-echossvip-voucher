<?php

namespace CHYP\Partner\Echooss\Voucher\Application\Factory;

use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\AccumulatePointUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\DepletePointUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\RewardsCardUseCaseInterface;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;

/**
 * RewardsCard UseCase 工廠。
 *
 * 負責根據 action 字串建立對應的 RewardsCardUseCase 實例。
 * 將 UseCase 的建立邏輯從 Core 類別中抽離，遵守單一職責原則。
 */
class RewardsCardUseCaseFactory implements UseCaseFactoryInterface
{
    /**
     * Action 對應 UseCase 類別的映射表。
     *
     * @var array<string, class-string<RewardsCardUseCaseInterface>>
     */
    protected array $useCaseMap = [
        'accumulatePoint' => AccumulatePointUseCase::class,
        'depletePoint'    => DepletePointUseCase::class,
    ];

    /**
     * 根據 action 名稱建立對應的 RewardsCardUseCase 實例。
     *
     * @param string $action  動作名稱。
     * @param array  $payload 請求資料陣列。
     *
     * @return RewardsCardUseCaseInterface
     *
     * @throws RequestTypeException 當 action 不存在時。
     */
    public function create(string $action, $payload): RewardsCardUseCaseInterface
    {
        $this->validateAction($action);
        $this->validatePayload($payload);

        $useCaseClass = $this->useCaseMap[$action];

        return new $useCaseClass($payload);
    }

    /**
     * 檢查是否支援指定的 action。
     *
     * @param string $action 動作名稱。
     *
     * @return boolean
     */
    public function supports(string $action): bool
    {
        return isset($this->useCaseMap[$action]);
    }

    /**
     * 取得所有支援的 action 名稱。
     *
     * @return array<string>
     */
    public function supportedActions(): array
    {
        return array_keys($this->useCaseMap);
    }

    /**
     * 驗證 action 是否有效。
     *
     * @param string $action 動作名稱。
     *
     * @return void
     *
     * @throws RequestTypeException 當 action 不存在時。
     */
    protected function validateAction(string $action): void
    {
        if (!$this->supports($action)) {
            throw new RequestTypeException(
                sprintf(
                    'Request action "%s" not exists. Available actions: %s',
                    $action,
                    implode(', ', $this->supportedActions())
                )
            );
        }
    }

    /**
     * 驗證 payload 是否為有效的陣列。
     *
     * @param mixed $payload 請求資料。
     *
     * @return void
     *
     * @throws RequestTypeException 當 payload 不是陣列時。
     */
    protected function validatePayload($payload): void
    {
        if (!is_array($payload)) {
            throw new RequestTypeException('RewardsCard request payload must be an array.');
        }
    }
}
