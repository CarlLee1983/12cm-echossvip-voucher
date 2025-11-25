<?php

namespace CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher;

use CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface;

abstract class AbstractVoucherUseCase implements VoucherUseCaseInterface
{
    protected RequestInterface $request;

    /**
     * @param \CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface $request Request DTO.
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritDoc}
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface
     */
    public function payload()
    {
        return $this->request;
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
     * Internal path definition.
     *
     * @return string
     */
    abstract protected function pathValue(): string;

    /**
     * Internal response-type key definition.
     *
     * @return string
     */
    abstract protected function responseTypeValue(): string;
}
