<?php

namespace CHYP\Partner\Echooss\Voucher\Application\Factory;

use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;
use CHYP\Partner\Echooss\Voucher\Type\Response\AccumulatePoint;
use CHYP\Partner\Echooss\Voucher\Type\Response\CreateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\DepletePoint;
use CHYP\Partner\Echooss\Voucher\Type\Response\ExecuteRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\FreezeRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\QueryRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\QueryRedeemBatchDetail;
use CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface;
use CHYP\Partner\Echooss\Voucher\Type\Response\ReverseRedeem;
use CHYP\Partner\Echooss\Voucher\Type\Response\UpdateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Response\Voucher;
use CHYP\Partner\Echooss\Voucher\Type\Response\VoucherList;

class ResponseFactory
{
    /**
     * @var array<string,class-string<ResponseInterface>>
     */
    protected array $map = [
        'voucherList'            => VoucherList::class,
        'createRedeemBatch'      => CreateRedeemBatch::class,
        'queryRedeemBatch'       => QueryRedeemBatch::class,
        'queryRedeemBatchDetail' => QueryRedeemBatchDetail::class,
        'freezeRedeemBatch'      => FreezeRedeemBatch::class,
        'updateRedeemBatch'      => UpdateRedeemBatch::class,
        'executeRedeemBatch'     => ExecuteRedeemBatch::class,
        'reverseRedeem'          => ReverseRedeem::class,
        'accumulatePoint'        => AccumulatePoint::class,
        'depletePoint'           => DepletePoint::class,
        'voucher'                => Voucher::class,
    ];

    /**
     * Instantiate response class by type key.
     *
     * @param string $type Response type key.
     *
     * @return \CHYP\Partner\Echooss\Voucher\Type\Response\ResponseInterface
     */
    public function create(string $type): ResponseInterface
    {
        if (!array_key_exists($type, $this->map)) {
            throw new RequestTypeException('Response type not registered. type: ' . $type);
        }

        $className = $this->map[$type];

        return new $className();
    }
}
