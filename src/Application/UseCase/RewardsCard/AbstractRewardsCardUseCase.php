<?php

namespace CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard;

abstract class AbstractRewardsCardUseCase implements RewardsCardUseCaseInterface
{
    protected array $requests;

    /**
     * @param array $requests List of request DTOs.
     */
    public function __construct(array $requests)
    {
        $this->requests = $requests;
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function payload()
    {
        return ['data' => $this->requests];
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function path(): string
    {
        return $this->pathValue();
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function responseType(): string
    {
        return $this->responseTypeValue();
    }

    /**
     * Internal API path value.
     *
     * @return string
     */
    abstract protected function pathValue(): string;

    /**
     * Internal response-type key value.
     *
     * @return string
     */
    abstract protected function responseTypeValue(): string;
}
