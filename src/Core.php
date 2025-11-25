<?php

namespace CHYP\Partner\Echooss\Voucher;

use CHYP\Partner\Echooss\Voucher\Application\ApiContext;
use CHYP\Partner\Echooss\Voucher\Application\Assembler\RequestAssembler;
use CHYP\Partner\Echooss\Voucher\Application\Factory\ResponseFactory;
use CHYP\Partner\Echooss\Voucher\Application\Hydrator\ResponseHydrator;
use CHYP\Partner\Echooss\Voucher\Application\Service\RewardsCardService;
use CHYP\Partner\Echooss\Voucher\Application\Service\VoucherService;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\AccumulatePointUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\DepletePointUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\RewardsCard\RewardsCardUseCaseInterface;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\CreateRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\ExecuteRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\FreezeRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\QueryRedeemBatchDetailUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\QueryRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\ReverseRedeemUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\UpdateRedeemBatchUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\VoucherListUseCase;
use CHYP\Partner\Echooss\Voucher\Application\UseCase\Voucher\VoucherUseCaseInterface;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;
use CHYP\Partner\Echooss\Voucher\Exception\ResponseTypeException;
use CHYP\Partner\Echooss\Voucher\Infrastructure\Http\RewardsCardGateway;
use CHYP\Partner\Echooss\Voucher\Infrastructure\Http\VoucherGateway;
use CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface;
use CHYP\Partner\Echooss\Voucher\Type\Response;
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

    /**
     * @param boolean                 $isSandBox          Use sandbox endpoints.
     * @param ApiContext|null         $context            Pre-configured API context.
     * @param ClientInterface|null    $httpClient         Custom HTTP client.
     * @param VoucherService|null     $voucherService     Custom voucher service.
     * @param RewardsCardService|null $rewardsCardService Custom rewards-card service.
     */
    public function __construct(
        bool $isSandBox = false,
        ?ApiContext $context = null,
        ?ClientInterface $httpClient = null,
        ?VoucherService $voucherService = null,
        ?RewardsCardService $rewardsCardService = null
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
            : $this->buildVoucherUseCase($action, $param);

        return $this->voucherService->handle($useCase);
    }

    /**
     * Legacy helper returning aggregated Type\Response object.
     *
     * @deprecated Prefer {@see voucher()} which returns concrete ResponseInterface.
     *
     * @param string|VoucherUseCaseInterface $action Action string or use case.
     * @param RequestInterface|null          $param  Request DTO when using string action.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response
     */
    public function voucherLegacy($action, RequestInterface $param = null): Response
    {
        $useCase = $action instanceof VoucherUseCaseInterface
            ? $action
            : $this->buildVoucherUseCase($action, $param);

        $raw = $this->voucherService->requestRaw($useCase);

        return new Response($useCase->responseType(), $raw);
    }

    /**
     * Execute Echoss rewards-card API.
     *
     * @param string|RewardsCardUseCaseInterface $action Action string or use case.
     * @param array                              $param  Request payload array.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface
     */
    public function rewardsCard($action, array $param): ResponseInterface
    {
        $useCase = $action instanceof RewardsCardUseCaseInterface
            ? $action
            : $this->buildRewardsCardUseCase($action, $param);

        return $this->rewardsCardService->handle($useCase);
    }

    /**
     * Legacy helper returning Type\Response for rewards card.
     *
     * @deprecated Prefer {@see rewardsCard()} which returns concrete ResponseInterface.
     *
     * @param string|RewardsCardUseCaseInterface $action Action string or use case.
     * @param array                              $param  Request payload array.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response
     */
    public function rewardsCardLegacy($action, array $param): Response
    {
        $useCase = $action instanceof RewardsCardUseCaseInterface
            ? $action
            : $this->buildRewardsCardUseCase($action, $param);

        $raw = $this->rewardsCardService->requestRaw($useCase);

        return new Response($useCase->responseType(), $raw);
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
     * Resolve voucher use case instance.
     *
     * @param string                $action  Action key.
     * @param RequestInterface|null $request Request DTO.
     *
     * @return VoucherUseCaseInterface
     */
    protected function buildVoucherUseCase(string $action, ?RequestInterface $request): VoucherUseCaseInterface
    {
        $map = [
            'voucherList'            => VoucherListUseCase::class,
            'createRedeemBatch'      => CreateRedeemBatchUseCase::class,
            'queryRedeemBatch'       => QueryRedeemBatchUseCase::class,
            'queryRedeemBatchDetail' => QueryRedeemBatchDetailUseCase::class,
            'freezeRedeemBatch'      => FreezeRedeemBatchUseCase::class,
            'updateRedeemBatch'      => UpdateRedeemBatchUseCase::class,
            'executeRedeemBatch'     => ExecuteRedeemBatchUseCase::class,
            'reverseRedeem'          => ReverseRedeemUseCase::class,
        ];

        if (!isset($map[$action])) {
            throw new RequestTypeException('Request action not exists.');
        }

        if (!$request instanceof RequestInterface) {
            throw new RequestTypeException('Voucher request payload is required.');
        }

        $className = $map[$action];

        return new $className($request);
    }

    /**
     * Resolve rewards-card use case instance.
     *
     * @param string $action Action key.
     * @param array  $param  Payload array.
     *
     * @return RewardsCardUseCaseInterface
     */
    protected function buildRewardsCardUseCase(string $action, array $param): RewardsCardUseCaseInterface
    {
        $map = [
            'accumulatePoint' => AccumulatePointUseCase::class,
            'depletePoint'    => DepletePointUseCase::class,
        ];

        if (!isset($map[$action])) {
            throw new RequestTypeException('Request action not exists.');
        }

        $className = $map[$action];

        return new $className($param);
    }
}
