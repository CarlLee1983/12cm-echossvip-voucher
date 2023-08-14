<?php

namespace CHYP\Partner\Echooss\Voucher;

use CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;
use CHYP\Partner\Echooss\Voucher\Exception\ResponseTypeException;
use CHYP\Partner\Echooss\Voucher\Type\Response;


class Core
{
    /**
     * Sandbox model.
     *
     * @var boolean
     */
    public bool $sandbox;

    /**
     * Production model host.
     *
     * @var string
     */
    private $prodHost = 'https://service.12cm.com.tw';

    /**
     * Sandbox model host.
     *
     * @var string
     */
    private $devHost = 'https://testservice.12cm.com.tw';

    /**
     * Th echoss vip platform api host.
     *
     * @var string
     */
    private string $apiHost;

    /**
     * __construct
     *
     * @param boolean $isSandBox
     */
    public function __construct(bool $isSandBox = false)
    {
        $this->apiHost = $isSandBox
        ? $this->devHost
        : $this->prodHost;
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
     * @param string $path
     * @param array $content
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    public function request(string $path = '', array $content = []): PsrResponse
    {
        try {
            return (new Client)->request(
                'POST',
                $this->apiHost . $path,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->getToken(),
                    ],
                    'body' => json_encode($content),
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
     * @param string $action
     * @param \CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface $param
     *
     * @return mixed
     */
    public function voucher(string $action, RequestInterface $param): Response
    {
        return (new Voucher($this))->do($action, $param);
    }
}
