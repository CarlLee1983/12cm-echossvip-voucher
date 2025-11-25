<?php

namespace CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher;

class QueryRedeemBatchUseCase extends AbstractVoucherUseCase
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function pathValue(): string
    {
        return '/pos_gateway/api/query-redeem-batch';
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function responseTypeValue(): string
    {
        return 'queryRedeemBatch';
    }
}
