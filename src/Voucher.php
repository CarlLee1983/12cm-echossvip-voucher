<?php

namespace CHYP\Partner\Echooss\Voucher;

use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;
use CHYP\Partner\Echooss\Voucher\Type\Response;

class Voucher
{
    /**
     * Request path prefix for voucher APIs.
     *
     * @var string
     */
    protected string $apiPrefix = '/pos_gateway/api';

    /**
     * Maps action names to their specific API endpoint paths for voucher services.
     *
     * @var array<string, string>
     */
    protected array $requestPath = [
        'voucherList'            => '/voucher-list',
        'createRedeemBatch'      => '/create-redeem-batch',
        'queryRedeemBatch'       => '/query-redeem-batch',
        'queryRedeemBatchDetail' => '/query-redeem-batch-detail',
        'freezeRedeemBatch'      => '/freeze-redeem-batch',
        'updateRedeemBatch'      => '/update-redeem-batch',
        'executeRedeemBatch'     => '/execute-redeem-batch',
        'reverseRedeem'          => '/reverse-redeem',
    ];

    /**
     * Voucher constructor.
     *
     * @param \CHYP\Partner\Echooss\Voucher\Core $core The Core instance for API communication.
     */
    public function __construct(Core $core)
    {
        $this->core = $core;
    }

    /**
     * Perform a voucher API request.
     *
     * This method centralizes API requests for voucher actions. It uses the `requestPath`
     * map to find the correct API endpoint for the given action. The response data
     * is typically extracted from the 'data' key of the JSON decoded response.
     *
     * @param string $action The API action to perform (e.g., 'voucherList', 'createRedeemBatch').
     * @param array  $param  The data payload for the request.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response The API response.
     * @throws \CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException If the action is not defined in `requestPath`.
     */
    public function do(string $action, array $param): Response
    {
        if (!array_key_exists($action, $this->requestPath)) {
            throw new RequestTypeException('Request action [' . $action . '] not exists in Voucher.');
        }

        $response = $this->core->request(
            'POST',
            $this->apiPrefix . $this->requestPath[$action],
            $param
        );

        return new Response($action, json_decode($response->getBody()->getContents(), true)['data'] ?? []);
    }
}
