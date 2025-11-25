<?php

namespace CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher;

class ReverseRedeemUseCase extends AbstractVoucherUseCase
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function pathValue(): string
    {
        return '/pos_gateway/api/reverse-redeem';
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function responseTypeValue(): string
    {
        return 'reverseRedeem';
    }
}
