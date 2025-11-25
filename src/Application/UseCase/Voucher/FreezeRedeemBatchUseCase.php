<?php

namespace CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher;

class FreezeRedeemBatchUseCase extends AbstractVoucherUseCase
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function pathValue(): string
    {
        return '/pos_gateway/api/freeze-redeem-batch';
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function responseTypeValue(): string
    {
        return 'freezeRedeemBatch';
    }
}
