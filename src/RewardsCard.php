<?php

namespace CHYP\Partner\Echooss\Voucher;

use CHYP\Partner\Echooss\Voucher\Type\Response;
use CHYP\Partner\Echooss\Voucher\Type\Request\RequestInterface;

class RewardsCard
{
    /**
     * Request path prefix.
     *
     * @var string
     */
    protected string $apiPrefix = '/api/pos';

    /**
     * __construct
     *
     * @param Core $core
     */
    public function __construct(Core $core)
    {
        $this->core = $core;
        $this->core->apiHost = 'https://stagevip-api.12cm.com.tw';
    }

    /**
     * Do request.
     *
     * @param string $action
     * @param array $data
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response
     */
    public function do(string $action, array $data): Response
    {
        if ($action == 'accumulatePoint') {
            return $this->accumulate($data);
        }

        if ($action == 'depletePoint') {
            return $this->deplete($data);
        }
    }

    /**
     * Echoss VIP Member Loyalty Card: New Purchase Added (Points Earned for Minimum Spending.
     *
     * @param array $data
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response
     */
    public function accumulate(array $data): Response
    {
        $response = $this->core->request(
            'POST',
            $this->apiPrefix . '/mps-card-send-point',
            [
                'data' => $data,
            ]
        );

        return new Response('accumulatePoint', json_decode($response->getBody(), true));
    }

    /**
     * Deduct Echoss VIP Member Loyalty Card Points.
     *
     * @param array $data
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response
     */
    public function deplete(array $data): Response
    {
        $response = $this->core->request(
            'POST',
            $this->apiPrefix . '/mps-card-deduct-point',
            [
                'data' => $data,
            ]
        );

        return new Response('depletePoint', json_decode($response->getBody(), true));
    }
}
