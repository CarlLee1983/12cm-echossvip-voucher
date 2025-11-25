<?php

namespace CHYP\Partner\Echooss\Voucher\Application\Service;

use CHYP\Partner\Echooss\Voucher\Application\Assembler\RequestAssembler;
use CHYP\Partner\Echooss\Voucher\Application\Gateway\RewardsCardGatewayInterface;
use CHYP\Partner\Echooss\Voucher\Application\Hydrator\ResponseHydrator;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\RewardsCardUseCaseInterface;
use CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface;

class RewardsCardService
{
    protected RewardsCardGatewayInterface $gateway;
    protected RequestAssembler $requestAssembler;
    protected ResponseHydrator $responseHydrator;

    /**
     * @param RewardsCardGatewayInterface $gateway          Gateway implementation.
     * @param RequestAssembler            $requestAssembler Payload assembler.
     * @param ResponseHydrator            $responseHydrator Response hydrator.
     */
    public function __construct(
        RewardsCardGatewayInterface $gateway,
        RequestAssembler $requestAssembler,
        ResponseHydrator $responseHydrator
    ) {
        $this->gateway = $gateway;
        $this->requestAssembler = $requestAssembler;
        $this->responseHydrator = $responseHydrator;
    }

    /**
     * Execute rewards-card use case and hydrate response DTO.
     *
     * @param RewardsCardUseCaseInterface $useCase Rewards-card use case.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface
     */
    public function handle(RewardsCardUseCaseInterface $useCase): ResponseInterface
    {
        $response = $this->requestRaw($useCase);

        return $this->responseHydrator->hydrate($useCase->responseType(), $response);
    }

    /**
     * Execute use case and return raw API data (legacy compatibility).
     *
     * @param RewardsCardUseCaseInterface $useCase Rewards-card use case.
     *
     * @return array
     */
    public function requestRaw(RewardsCardUseCaseInterface $useCase): array
    {
        $payload = $this->requestAssembler->toArray($useCase->payload());

        return $this->gateway->post($useCase->path(), $payload);
    }
}
