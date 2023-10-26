<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Response;

use CHYP\Partner\Echooss\Voucher\Utils;

class QueryRedeemBatchDetail extends Response
{
    /**
     * API request.
     *
     * @var bool
     */
    public bool $success = false;

    /**
     * Redeem batch details.
     *
     * @param array $details
     *
     * @return array
     */
    public function details(array $details): array
    {
        $response = [];

        foreach ($details as $detail) {
            $data = new RedeemBatchDetail();

            foreach ($detail as $key => $value) {
                $key = Utils::camelCase($key);

                $data->$key = $value;
            }

            $response[] = $data;
        }

        return $response;
    }
}
