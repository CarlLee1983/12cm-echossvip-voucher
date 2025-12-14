<?php

namespace CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard;

use CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface;

abstract class AbstractRewardsCardUseCase implements RewardsCardUseCaseInterface
{
    protected RequestInterface $request;

    /**
     * @param RequestInterface $request Request DTO.
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function payload()
    {
        return ['data' => [$this->request]];
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
