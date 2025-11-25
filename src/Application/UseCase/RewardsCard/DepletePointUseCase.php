<?php

namespace CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard;

class DepletePointUseCase extends AbstractRewardsCardUseCase
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function pathValue(): string
    {
        return '/api/pos/mps-card-deduct-point';
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function responseTypeValue(): string
    {
        return 'depletePoint';
    }
}
