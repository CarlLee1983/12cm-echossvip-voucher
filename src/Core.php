<?php

namespace CHYP\Partner\Echooss\Voucher;

use CHYP\Partner\Echooss\Voucher\Application\ApiContext;
use CHYP\Partner\Echooss\Voucher\Application\Assembler\RequestAssembler;
use CHYP\Partner\Echooss\Voucher\Application\Factory\RewardsCardUseCaseFactory;
use CHYP\Partner\Echooss\Voucher\Application\Factory\ResponseFactory;
use CHYP\Partner\Echooss\Voucher\Application\Factory\VoucherUseCaseFactory;
use CHYP\Partner\Echooss\Voucher\Application\Hydrator\ResponseHydrator;
use CHYP\Partner\Echooss\Voucher\Application\Service\RewardsCardService;
use CHYP\Partner\Echooss\Voucher\Application\Service\VoucherService;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\RewardsCardUseCaseInterface;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\VoucherUseCaseInterface;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;
use CHYP\Partner\Echooss\Voucher\Exception\ResponseTypeException;
use CHYP\Partner\Echooss\Voucher\Infrastructure\Http\RewardsCardGateway;
use CHYP\Partner\Echooss\Voucher\Infrastructure\Http\VoucherGateway;
use CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface;
use CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface as PsrResponse;

class Core
{
    protected ApiContext $context;
    protected VoucherService $voucherService;
    protected RewardsCardService $rewardsCardService;
    protected RequestAssembler $requestAssembler;
    protected ClientInterface $httpClient;
    protected VoucherUseCaseFactory $voucherUseCaseFactory;
    protected RewardsCardUseCaseFactory $rewardsCardUseCaseFactory;

    /**
     * @param boolean                        $isSandBox                 Use sandbox endpoints.
     * @param ApiContext|null                $context                   Pre-configured API context.
     * @param ClientInterface|null           $httpClient                Custom HTTP client.
     * @param VoucherService|null            $voucherService            Custom voucher service.
     * @param RewardsCardService|null        $rewardsCardService        Custom rewards-card service.
     * @param VoucherUseCaseFactory|null     $voucherUseCaseFactory     Custom voucher use case factory.
     * @param RewardsCardUseCaseFactory|null $rewardsCardUseCaseFactory Custom rewards-card use case factory.
     */
    public function __construct(
        bool $isSandBox = false,
        ?ApiContext $context = null,
        ?ClientInterface $httpClient = null,
        ?VoucherService $voucherService = null,
        ?RewardsCardService $rewardsCardService = null,
        ?VoucherUseCaseFactory $voucherUseCaseFactory = null,
        ?RewardsCardUseCaseFactory $rewardsCardUseCaseFactory = null
    ) {
        $this->context = $context ?? new ApiContext($isSandBox);
        $this->requestAssembler = new RequestAssembler();
        $this->httpClient = $httpClient ?? new Client(['timeout' => $this->context->timeout()]);

        $responseFactory = new ResponseFactory();
        $responseHydrator = new ResponseHydrator($responseFactory);

        $this->voucherService = $voucherService ?? new VoucherService(
            new VoucherGateway($this->httpClient, $this->context),
            $this->requestAssembler,
            $responseHydrator
        );

        $this->rewardsCardService = $rewardsCardService ?? new RewardsCardService(
            new RewardsCardGateway($this->httpClient, $this->context),
            $this->requestAssembler,
            $responseHydrator
        );

        // 注入 UseCase 工廠，遵守單一職責原則
        $this->voucherUseCaseFactory = $voucherUseCaseFactory ?? new VoucherUseCaseFactory();
        $this->rewardsCardUseCaseFactory = $rewardsCardUseCaseFactory ?? new RewardsCardUseCaseFactory();
    }

    /**
     * Determine whether sandbox environment is enabled.
     *
     * @return boolean
     */
    public function useSandbox(): bool
    {
        return $this->context->useSandbox();
    }

    /**
     * Retrieve API authentication token.
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->context->token();
    }

    /**
     * Set API authentication token.
     *
     * @param string $token Bearer token string.
     *
     * @return self
     */
    public function setToken(string $token): self
    {
        $this->context->setToken($token);

        return $this;
    }

    /**
     * Low-level HTTP request helper.
     *
     * @param string $method  HTTP method.
     * @param string $path    API path.
     * @param array  $content JSON content payload.
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    public function request(string $method, string $path, array $content = []): PsrResponse
    {
        try {
            $isRewardsCard = strpos($path, '/api/pos') === 0;
            $baseUri = $isRewardsCard
                ? $this->context->rewardsCardBaseUri()
                : $this->context->voucherBaseUri();

            return $this->httpClient->request(
                $method,
                $baseUri . $path,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->getToken(),
                    ],
                    'json' => $content,
                ]
            );
        } catch (RequestException $exception) {
            if ($exception->hasResponse()) {
                $response = $exception->getResponse();

                throw new ResponseTypeException($response->getBody()->getContents(), $response->getStatusCode());
            }

            throw new RequestTypeException('Request Error.');
        }
    }

    /**
     * Execute Echoss voucher API.
     *
     * @param string|VoucherUseCaseInterface $action Action string or use case.
     * @param RequestInterface|null          $param  Request DTO when using string action.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface
     */
    public function voucher($action, RequestInterface $param = null): ResponseInterface
    {
        $useCase = $action instanceof VoucherUseCaseInterface
            ? $action
            : $this->voucherUseCaseFactory->create($action, $param);

        return $this->voucherService->handle($useCase);
    }

    /**
     * Execute Echoss rewards-card API.
     *
     * @param string|RewardsCardUseCaseInterface $action Action string or use case.
     * @param RequestInterface                   $param  Request payload DTO.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface
     */
    public function rewardsCard($action, RequestInterface $param): ResponseInterface
    {
        $useCase = $action instanceof RewardsCardUseCaseInterface
            ? $action
            : $this->rewardsCardUseCaseFactory->create($action, $param);

        return $this->rewardsCardService->handle($useCase);
    }

    /**
     * Deconstruct request/response objects into array payload.
     *
     * @param RequestInterface|ResponseInterface|array $row Mixed payload.
     *
     * @return array
     */
    public function deepDeconstruction($row)
    {
        return $this->requestAssembler->toArray($row);
    }

    /**
     * 取得 Voucher UseCase 工廠實例。
     *
     * @return VoucherUseCaseFactory
     */
    public function getVoucherUseCaseFactory(): VoucherUseCaseFactory
    {
        return $this->voucherUseCaseFactory;
    }

    /**
     * 取得 RewardsCard UseCase 工廠實例。
     *
     * @return RewardsCardUseCaseFactory
     */
    public function getRewardsCardUseCaseFactory(): RewardsCardUseCaseFactory
    {
        return $this->rewardsCardUseCaseFactory;
    }
}
