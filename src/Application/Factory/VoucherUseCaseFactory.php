<?php

namespace CHYP\Partner\Echooss\Voucher\Application\Factory;

use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\CreateRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\ExecuteRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\FreezeRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\QueryRedeemBatchDetailUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\QueryRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\ReverseRedeemUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\UpdateRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\VoucherListUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\VoucherUseCaseInterface;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;
use CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface;

/**
 * Voucher UseCase 工廠。
 *
 * 負責根據 action 字串建立對應的 VoucherUseCase 實例。
 * 將 UseCase 的建立邏輯從 Core 類別中抽離，遵守單一職責原則。
 */
class VoucherUseCaseFactory implements UseCaseFactoryInterface
{
    /**
     * Action 對應 UseCase 類別的映射表。
     *
     * @var array<string, class-string<VoucherUseCaseInterface>>
     */
    protected array $useCaseMap = [
        'voucherList'            => VoucherListUseCase::class,
        'createRedeemBatch'      => CreateRedeemBatchUseCase::class,
        'queryRedeemBatch'       => QueryRedeemBatchUseCase::class,
        'queryRedeemBatchDetail' => QueryRedeemBatchDetailUseCase::class,
        'freezeRedeemBatch'      => FreezeRedeemBatchUseCase::class,
        'updateRedeemBatch'      => UpdateRedeemBatchUseCase::class,
        'executeRedeemBatch'     => ExecuteRedeemBatchUseCase::class,
        'reverseRedeem'          => ReverseRedeemUseCase::class,
    ];

    /**
     * 根據 action 名稱建立對應的 VoucherUseCase 實例。
     *
     * @param string                $action  動作名稱。
     * @param RequestInterface|null $payload 請求 DTO。
     *
     * @return VoucherUseCaseInterface
     *
     * @throws RequestTypeException 當 action 不存在或 payload 無效時。
     */
    public function create(string $action, $payload): VoucherUseCaseInterface
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
     * 驗證 payload 是否為有效的 RequestInterface。
     *
     * @param mixed $payload 請求資料。
     *
     * @return void
     *
     * @throws RequestTypeException 當 payload 無效時。
     */
    protected function validatePayload($payload): void
    {
        if (!$payload instanceof RequestInterface) {
            throw new RequestTypeException('Voucher request payload is required and must implement RequestInterface.');
        }
    }
}
