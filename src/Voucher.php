<?php

namespace CHYP\Partner\Echooss\Voucher;

use CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface;
use CHYP\Partner\Echooss\Voucher\Type\Response;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;

class Voucher
{
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
     * Request path prefix.
     *
     * @var string
     */
    protected string $apiPrefix = '/pos_gateway/api';

    /**
     * Request path.
     *
     * @var array
     */
    protected array $requestPath = [
        'voucherList' => '/voucher-list',
        'createRedeemBatch' => '/create-redeem-batch',
        'queryRedeemBatch' => '/query-redeem-batch',
        'queryRedeemBatchDetail' => '/query-redeem-batch-detail',
        'freezeRedeemBatch' => '/freeze-redeem-batch',
        'updateRedeemBatch' => '/update-redeem-batch',
        'executeRedeemBatch' => 'execute-redeem-batch',
        'reverseRedeem' => '/reverse-redeem',
    ];

    /**
     * __construct
     *
     * @param Core $core
     */
    public function __construct(Core $core)
    {
        $this->core = $core;

        if ($this->core->isSandBox) {
            $this->core->apiHost = $this->devHost;
        } else {
            $this->core->apiHost = $this->prodHost;
        }
    }

    /**
     * Call api by action.
     *
     * @param string $action
     * @param array $param
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response
     */
    public function do(string $action, array $param): Response
    {
        if (!array_key_exists($action, $this->requestPath)) {
            throw new RequestTypeException('Request action not exists.');
        }

        $response = $this->core->request(
            'POST',
            $this->apiPrefix . $this->requestPath[$action],
            $param
        );

        return new Response($action, json_decode($response->getBody(), true)['data'] ?? []);
    }
}
