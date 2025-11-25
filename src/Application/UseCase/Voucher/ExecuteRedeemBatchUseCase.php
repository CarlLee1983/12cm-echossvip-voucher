<?php

namespace CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher;

class ExecuteRedeemBatchUseCase extends AbstractVoucherUseCase
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function pathValue(): string
    {
        return '/pos_gateway/api/execute-redeem-batch';
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function responseTypeValue(): string
    {
        return 'executeRedeemBatch';
    }
}
