<?php

namespace CHYP\Partner\Echooss\Voucher;

use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;
use CHYP\Partner\Echooss\Voucher\Exception\ResponseTypeException;
use CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface;
use CHYP\Partner\Echooss\Voucher\Type\Response;
use CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface as PsrResponse;

class Core
{
    /**
     * Sandbox model.
     *
     * @var bool
     */
    public bool $isSandBox;

    /**
     * The Echooss VIP platform API host.
     *
     * @var string
     */
    public string $apiHost;

    /**
     * API authentication token.
     *
     * @var string|null
     */
    private ?string $token = null;

    /**
     * Service type ('voucher' or 'rewardsCard').
     * Determines which API host to use.
     *
     * @var string
     */
    private string $serviceType;

    /**
     * Production host for voucher services.
     *
     * @var string
     */
    private string $prodHost = 'https://service.12cm.com.tw';

    /**
     * Development/Sandbox host for voucher services.
     *
     * @var string
     */
    private string $devHost = 'https://testservice.12cm.com.tw';

    /**
     * Production host for rewards card services.
     *
     * @var string
     */
    private string $rewardsCardProdHost = 'https://vip-api.12cm.com.tw';

    /**
     * Development/Sandbox host for rewards card services.
     *
     * @var string
     */
    private string $rewardsCardDevHost = 'https://stagevip-api.12cm.com.tw';

    /**
     * Core constructor.
     *
     * @param bool   $isSandBox    Whether to use the sandbox environment. Defaults to false.
     * @param string $serviceType  The type of service to use ('voucher' or 'rewardsCard'). Defaults to 'voucher'.
     */
    public function __construct(bool $isSandBox = false, string $serviceType = 'voucher')
    {
        $this->isSandBox = $isSandBox;
        $this->serviceType = $serviceType;

        if ($this->serviceType === 'rewardsCard') {
            $this->apiHost = $this->isSandBox ? $this->rewardsCardDevHost : $this->rewardsCardProdHost;
        } else {
            $this->apiHost = $this->isSandBox ? $this->devHost : $this->prodHost;
        }
    }

    /**
     * Get API authentication token.
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token ?? '';
    }

    /**
     * Set api authentication token.
     *
     * @param string $token
     *
     * @return self
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Request.
     *
     * @param string $method
     * @param string $path
     * @param array  $content
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    public function request(string $method, string $path, array $content = []): PsrResponse
    {
        try {
            return (new Client())->request(
                $method,
                $this->apiHost.$path,
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$this->getToken(),
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
     * Perform an API request for voucher services.
     *
     * @param string                                                              $action The API action to perform.
     * @param \CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface $param  The request parameters.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response The API response.
     */
    public function voucher(string $action, RequestInterface $param): Response
    {
        return (new Voucher($this))->do($action, $this->normalizeToArrayRecursive($param));
    }

    /**
     * Perform an API request for rewards card services.
     *
     * @param string $action The API action to perform.
     * @param array  $param  The request parameters.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response The API response.
     */
    public function rewardsCard(string $action, array $param): Response
    {
        return (new RewardsCard($this))->do($action, $this->normalizeToArrayRecursive($param));
    }

    /**
     * Recursively normalizes an object or array to an array.
     * Objects implementing RequestInterface or ResponseInterface are converted to arrays via their toArray() method.
     *
     * @param mixed $row The input to normalize. Can be an array or an object.
     *                   Expected to be array or instance of RequestInterface/ResponseInterface.
     *
     * @return array The normalized array.
     */
    public function normalizeToArrayRecursive($row): array
    {
        if (is_array($row)) {
            foreach ($row as &$item) {
                $item = $this->normalizeToArrayRecursive($item);
            }
            return $row;
        }

        if (is_a($row, RequestInterface::class) || is_a($row, ResponseInterface::class)) {
            return $this->normalizeToArrayRecursive($row->toArray());
        }

        // If $row is not an array and not one of our specific objects (RequestInterface/ResponseInterface),
        // it's considered a scalar or an unsupported object type.
        // To adhere to the 'array' return type, such values are wrapped in an array.
        return [$row];
    }
}
