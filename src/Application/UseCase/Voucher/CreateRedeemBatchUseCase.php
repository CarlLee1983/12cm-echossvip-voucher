<?php

namespace CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher;

class CreateRedeemBatchUseCase extends AbstractVoucherUseCase
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function pathValue(): string
    {
        return '/pos_gateway/api/create-redeem-batch';
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function responseTypeValue(): string
    {
        return 'createRedeemBatch';
    }
}
