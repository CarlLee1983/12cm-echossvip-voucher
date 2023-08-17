<?php

namespace CHYP\Partner\Echooss\Voucher\Type;

use CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface;
use CHYP\Partner\Echooss\Voucher\Type\Response\AccumulatePoint;
use CHYP\Partner\Echooss\Voucher\Type\Response\DepletePoint;
use CHYP\Partner\Echooss\Voucher\Type\Response\CreateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\ExecuteRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\FreezeRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\QueryRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\QueryRedeemBatchDetail;
use CHYP\Partner\Echooss\Voucher\Type\Response\ReverseRedeem;
use CHYP\Partner\Echooss\Voucher\Type\Response\UpdateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\Voucher;
use CHYP\Partner\Echooss\Voucher\Type\Response\VoucherList;
use CHYP\Partner\Echooss\Voucher\Utils;

class Response
{
    /**
     * Original Response params.
     *
     * @var array
     */
    public array $params = [];

    /**
     * Map response type.
     *
     * @var array
     */
    protected array $mapType = [
        'voucherList' => 'voucherList',
        'createRedeemBatch' => 'createRedeemBatch',
        'queryRedeemBatch' => 'queryRedeemBatch',
        'queryRedeemBatchDetail' => 'queryRedeemBatchDetail',
        'freezeRedeemBatch' => 'freezeRedeemBatch',
        'updateRedeemBatch' => 'updateRedeemBatch',
        'executeRedeemBatch' => 'executeRedeemBatch',
        'reverseRedeem' => 'reverseRedeem',
        'accumulatePoint' => 'accumulatePoint',
        'depletePoint' => 'depletePoint',
    ];

    /**
     * __construct
     *
     * @param string $type
     * @param array $params
     */
    public function __construct(string $type, array $params)
    {
        $this->type = $type;
        $this->params = $params;
    }

    /**
     * Format.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface
     */
    public function format(): ResponseInterface
    {
        $method = $this->mapType[$this->type];

        return $this->$method();
    }

    /**
     * Mapping object value.
     *
     * @param object $response
     * @param array $params
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface
     */
    protected function mappingValue($response, array $params): ResponseInterface
    {
        foreach ($params as $columnName => $value) {
            $columnName = Utils::camelCase($columnName);

            $response->$columnName = $value;
        }

        return $response;
    }

    /**
     * Voucher list response.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\VoucherList
     */
    public function voucherList(): VoucherList
    {
        $data = [];

        foreach ($this->params as $row) {
            $data[] = $this->mappingValue(new Voucher, $row);
        }

        return $this->mappingValue(new VoucherList, ['data' => $data]);
    }

    /**
     * The create redeem batch response.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\CreateRedeemBatch
     */
    public function createRedeemBatch(): CreateRedeemBatch
    {
        return $this->mappingValue(new CreateRedeemBatch, $this->params);
    }

    /**
     * Query redeem batch response.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\QueryRedeemBatch
     */
    public function queryRedeemBatch(): QueryRedeemBatch
    {
        return $this->mappingValue(new QueryRedeemBatch, $this->params);
    }

    /**
     * Query redeem batch detail response.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\QueryRedeemBatchDetail
     */
    public function queryRedeemBatchDetail(): QueryRedeemBatchDetail
    {
        return $this->mappingValue(new QueryRedeemBatchDetail, $this->params);
    }

    /**
     * Query redeem batch detail response.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\FreezeRedeemBatch
     */
    public function freezeRedeemBatch(): FreezeRedeemBatch
    {
        return $this->mappingValue(new FreezeRedeemBatch, $this->params);
    }

    /**
     * Query redeem batch detail response.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\UpdateRedeemBatch
     */
    public function updateRedeemBatch(): UpdateRedeemBatch
    {
        return $this->mappingValue(new UpdateRedeemBatch, $this->params);
    }

    /**
     * Query redeem batch detail response.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\ExecuteRedeemBatch
     */
    public function executeRedeemBatch(): ExecuteRedeemBatch
    {
        return $this->mappingValue(new ExecuteRedeemBatch, $this->params);
    }

    /**
     * Query redeem batch detail response.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\ReverseRedeem
     */
    public function reverseRedeem(): ReverseRedeem
    {
        return $this->mappingValue(new ReverseRedeem, $this->params);
    }

    /**
     * Echoss VIP Member Loyalty Card: New Purchase Added (Points Earned for Minimum Spending.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\AccumulatePoint
     */
    public function accumulatePoint(): AccumulatePoint
    {
        return $this->mappingValue(new AccumulatePoint, $this->params);
    }

    /**
     * Deduct Echoss VIP Member Loyalty Card Points.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\DepletePoint
     */
    public function depletePoint(): DepletePoint
    {
        return $this->mappingValue(new DepletePoint, $this->params);
    }
}
