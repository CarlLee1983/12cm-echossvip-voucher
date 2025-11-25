<?php

namespace CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard;

class AccumulatePointUseCase extends AbstractRewardsCardUseCase
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function pathValue(): string
    {
        return '/api/pos/mps-card-send-point';
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function responseTypeValue(): string
    {
        return 'accumulatePoint';
    }
}
