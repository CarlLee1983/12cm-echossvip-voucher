<?php

namespace CHYP\Partner\Echooss\Voucher;

use CHYP\Partner\Echooss\Voucher\Type\Response;

class RewardsCard
{
    /**
     * Request path prefix.
     *
     * @var string
     */
    protected string $apiPrefix = '/api/pos';

    /**
     * Build RewardsCard service.
     *
     * @param Core $core Echoss core facade.
     */
    public function __construct(Core $core)
    {
        $this->core = $core;
        $this->core->apiHost = 'https://stagevip-api.12cm.com.tw';
    }

    /**
     * Call RewardsCard API based on action.
     *
     * @param string $action API action name.
     * @param array  $data   Request payload.
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
     * Echoss VIP Member Loyalty Card: accumulate point.
     *
     * @param array $data Accumulation parameters.
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

        return new Response('accumulatePoint', json_decode($response->getBody(), true)['data'] ?? []);
    }

    /**
     * Deduct Echoss VIP Member Loyalty Card points.
     *
     * @param array $data Depletion parameters.
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
