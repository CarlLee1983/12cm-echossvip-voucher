<?php

namespace CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher;

class UpdateRedeemBatchUseCase extends AbstractVoucherUseCase
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function pathValue(): string
    {
        return '/pos_gateway/api/update-redeem-batch';
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function responseTypeValue(): string
    {
        return 'updateRedeemBatch';
    }
}
