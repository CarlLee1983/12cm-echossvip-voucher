<?php

namespace CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher;

class VoucherListUseCase extends AbstractVoucherUseCase
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function pathValue(): string
    {
        return '/pos_gateway/api/voucher-list';
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function responseTypeValue(): string
    {
        return 'voucherList';
    }
}
