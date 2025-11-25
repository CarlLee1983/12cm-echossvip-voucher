<?php

namespace CHYP\Partner\Echooss\Voucher;

use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;
use CHYP\Partner\Echooss\Voucher\Type\Response;

/**
 * Legacy Voucher HTTP gateway.
 *
 * @deprecated 此類別已被棄用，請改用 {@see Core::voucher()} 方法搭配 UseCase 模式。
 *             此類別將在未來版本中移除。
 *
 *             遷移範例：
 *             ```php
 *             // 舊的用法 (deprecated)
 *             $voucher = new Voucher($core);
 *             $response = $voucher->do('voucherList', ['phone_number' => '0912345678']);
 *
 *             // 新的用法 (recommended)
 *             $param = new VoucherList();
 *             $param->phoneNumber = '0912345678';
 *             $response = $core->voucher('voucherList', $param);
 *             ```
 */
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
     * Initialize voucher HTTP gateway.
     *
     * @param Core $core Echoss core facade.
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
     * Call voucher API by action.
     *
     * @param string $action Action name.
     * @param array  $param  Request payload.
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
