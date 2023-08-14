<?php

namespace CHYP\Partner\Echooss\Voucher;

use CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface;
use CHYP\Partner\Echooss\Voucher\Type\Response;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;

class Voucher
{
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
    }

    /**
     * Call api by action.
     *
     * @param string $action
     * @param \CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface $param
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response
     */
    public function do(string $action, RequestInterface $request): Response
    {
        if (!array_key_exists($action, $this->requestPath)) {
            throw new RequestTypeException('Request action not exists.');
        }

        $response = $this->core->request(
            $this->apiPrefix . $this->requestPath[$action],
            $request->toArray()
        );

        return new Response($action, json_decode($response->getBody(), true)['data'] ?? []);
    }
}
