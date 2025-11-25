<?php

namespace CHYP\Partner\Echooss\Voucher\Application\Service;

use CHYP\Partner\Echooss\Voucher\Application\Assembler\RequestAssembler;
use CHYP\Partner\Echooss\Voucher\Application\Gateway\VoucherGatewayInterface;
use CHYP\Partner\Echooss\Voucher\Application\Hydrator\ResponseHydrator;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\VoucherUseCaseInterface;
use CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface;

class VoucherService
{
    protected VoucherGatewayInterface $gateway;
    protected RequestAssembler $requestAssembler;
    protected ResponseHydrator $responseHydrator;

    /**
     * @param VoucherGatewayInterface $gateway          Gateway implementation.
     * @param RequestAssembler        $requestAssembler Payload assembler.
     * @param ResponseHydrator        $responseHydrator Response hydrator.
     */
    public function __construct(
        VoucherGatewayInterface $gateway,
        RequestAssembler $requestAssembler,
        ResponseHydrator $responseHydrator
    ) {
        $this->gateway = $gateway;
        $this->requestAssembler = $requestAssembler;
        $this->responseHydrator = $responseHydrator;
    }

    /**
     * Execute use case and hydrate response DTO.
     *
     * @param VoucherUseCaseInterface $useCase Voucher use case.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface
     */
    public function handle(VoucherUseCaseInterface $useCase): ResponseInterface
    {
        $response = $this->requestRaw($useCase);

        return $this->responseHydrator->hydrate($useCase->responseType(), $response);
    }

    /**
     * Execute use case and return raw API data (legacy compatibility).
     *
     * @param VoucherUseCaseInterface $useCase Voucher use case.
     *
     * @return array
     */
    public function requestRaw(VoucherUseCaseInterface $useCase): array
    {
        $payload = $this->requestAssembler->toArray($useCase->payload());

        return $this->gateway->post($useCase->path(), $payload);
    }
}
