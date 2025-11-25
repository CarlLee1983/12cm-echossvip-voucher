<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;

class FreezeRedeemBatch extends Request
{
    use StoreParamsTrait;

    /**
     * "Required, Unique Identifier of Redemption Order or Non-Member Product Voucher voucher_hash_id.
     *
     * @var string
     */
    public string $batchUuid = '';

    /**
     * Protected data.
     *
     * @var array
     */
    protected array $protectedData = [
        'freezeMins' => 1,
    ];

    /**
     * Required, freeze duration in minutes (1-60).
     *
     * @param integer $mins Freeze minutes.
     *
     * @return integer Valid minutes.
     */
    public function freezeMins(int $mins): int
    {
        if ($mins < 1 || $mins > 60) {
            throw new RequestTypeException('The freezeMins must be less than or equal to 60', 422);
        }

        return $mins;
    }
}
