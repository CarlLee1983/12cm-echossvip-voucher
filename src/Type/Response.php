<?php

namespace CHYP\Partner\Echooss\Voucher\Type;

use CHYP\Partner\Echooss\Voucher\Type\Response\CreateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\ExecuteRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\FreezeRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\QueryRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\QueryRedeemBatchDetail;
use CHYP\Partner\Echooss\Voucher\Type\Response\ReverseRedeem;
use CHYP\Partner\Echooss\Voucher\Type\Response\UpdateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\Voucher;
use CHYP\Partner\Echooss\Voucher\Utils;

class Response
{
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
     * @return mixed
     */
    public function format()
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
     * @return mixed
     */
    protected function mappingValue($response, array $params)
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
     * @return array
     */
    public function voucherList(): array
    {
        $data = [];

        foreach ($this->params as $row) {
            $data[] = $this->mappingValue(new Voucher, $row);
        }

        return $data;
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
    public function queryRedeemBatchDetail()
    {
        return $this->mappingValue(new QueryRedeemBatchDetail, $this->params);
    }

    /**
     * Query redeem batch detail response.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\FreezeRedeemBatch
     */
    public function freezeRedeemBatch()
    {
        return $this->mappingValue(new FreezeRedeemBatch, $this->params);
    }

    /**
     * Query redeem batch detail response.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\UpdateRedeemBatch
     */
    public function updateRedeemBatch()
    {
        return $this->mappingValue(new UpdateRedeemBatch, $this->params);
    }

    /**
     * Query redeem batch detail response.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\ExecuteRedeemBatch
     */
    public function executeRedeemBatch()
    {
        return $this->mappingValue(new ExecuteRedeemBatch, $this->params);
    }

    /**
     * Query redeem batch detail response.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\ReverseRedeem
     */
    public function reverseRedeem()
    {
        return $this->mappingValue(new ReverseRedeem, $this->params);
    }
}
