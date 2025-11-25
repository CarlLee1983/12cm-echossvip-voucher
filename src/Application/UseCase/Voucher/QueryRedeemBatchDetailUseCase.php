<?php

namespace CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher;

class QueryRedeemBatchDetailUseCase extends AbstractVoucherUseCase
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function pathValue(): string
    {
        return '/pos_gateway/api/query-redeem-batch-detail';
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function responseTypeValue(): string
    {
        return 'queryRedeemBatchDetail';
    }
}
