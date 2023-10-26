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
     * Th echoss vip platform api host.
     *
     * @var string
     */
    public string $apiHost;

    /**
     * __construct.
     *
     * @param bool $isSandBox
     */
    public function __construct(bool $isSandBox = false)
    {
        $this->isSandBox = $isSandBox;
    }

    /**
     * get api authentication token.
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
     * Echoss VIP Voucher api request.
     *
     * @param string                                                      $action
     * @param \CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface $param
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response
     */
    public function voucher(string $action, RequestInterface $param): Response
    {
        return (new Voucher($this))->do($action, $this->deepDeconstruction($param));
    }

    /**
     * Echoss VIP Member Rewards Card.
     *
     * @param string $action
     * @param array  $param
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response
     */
    public function rewardsCard(string $action, array $param): Response
    {
        return (new RewardsCard($this))->do($action, $this->deepDeconstruction($param));
    }

    /**
     * Deconstruction Request or Response Object.
     *
     * @param \CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface|\CHYP\Partner\Echooss\Voucher\Type\Request\ResponseInterface|array $row
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface|\CHYP\Partner\Echooss\Voucher\Type\Request\ResponseInterface|array
     */
    public function deepDeconstruction($row)
    {
        if (is_array($row)) {
            foreach ($row as &$item) {
                $item = $this->deepDeconstruction($item);
            }

            return $row;
        }

        if (is_a($row, RequestInterface::class) || is_a($row, ResponseInterface::class)) {
            return $this->deepDeconstruction($row->toArray());
        }

        return $row;
    }
}
