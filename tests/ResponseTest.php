<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    /**
     * Ensure voucher list response hydrates correctly.
     */
    public function testVoucherList()
    {
        $response = '{
            "data": [{
                "type": 2,
                "voucher_hash_id": "NhYNDyrdzG",
                "name": "測試商品券",
                "period_sales": 1,
                "sales_start_date": null,
                "sales_end_date": null,
                "images": [{
                    "id": 146,
                    "url": "https://www.google.com.tw",
                    "order": null
                }],
                "term_id": null,
                "total_count": 4,
                "unusable_count": 0,
                "redeemable_count": 4,
                "reverse_redeemable_count": 0,
                "voidable_count": 0,
                "reverse_voidable_count": 0,
                "start_date": "2022-11-08T00:00:00+08:00",
                "end_date": "2023-05-05T23:59:59+08:00",
                "phone_number": "0911425977"
            }]
        }';

        $response = json_decode($response, true);

        $data = (new CHYP\Partner\Echooss\Voucher\Type\Response('voucherList', $response['data'] ?? []))->format();
        $data = (new CHYP\Partner\Echooss\Voucher\Core())->deepDeconstruction($data->toArray());
        $firstVoucher = reset($data);

        $this->assertEquals($firstVoucher['voucherHashId'], 'NhYNDyrdzG');
    }

    /**
     * Ensure create redeem batch response works.
     */
    public function testRedeemBatch()
    {
        $response = '{
            "data": {
                "success": true,
                "message": "Successfully created : redeem details",
                "batch_token": "SNNL6EgbfLZCVLpN",
                "batch_uuid": "eLdo-20220926-DTtfhqEoaq"
            }
        }';
        $response = json_decode($response, true);

        $data = (new CHYP\Partner\Echooss\Voucher\Type\Response('createRedeemBatch', $response['data'] ?? []))->format();

        $this->assertTrue($data->success);
        $this->assertEquals($data->batchToken, 'SNNL6EgbfLZCVLpN');
    }

    /**
     * Ensure query redeem batch response works.
     */
    public function testQueryRedeemBatch()
    {
        $response = '{
            "data": {
                "success": true,
                "batch_uuid": "eLdo-20220926-DTtfhqEoaq"
            }
        }';
        $response = json_decode($response, true);

        $data = (new CHYP\Partner\Echooss\Voucher\Type\Response('queryRedeemBatch', $response['data'] ?? []))->format();

        $this->assertTrue($data->success);
        $this->assertEquals($data->batchUuid, 'eLdo-20220926-DTtfhqEoaq');
    }

    /**
     * Ensure query redeem batch detail response works.
     */
    public function testQueryRedeemBatchDetail()
    {
        $response = '{
            "data": {
                "success": true,
                "details": [
                    {
                    "redeem_type": 1,
                    "redeem_quantity": 1,
                    "voucher_hash_id": "lVWcq4PlVG",
                    "name": "三澧百元優惠券",
                    "coupon_type": 0,
                    "type_name": "優惠券",
                    "term_id": "D9341_A600"
                    },
                    {
                    "redeem_type": 2,
                    "redeem_quantity": 1,
                    "voucher_hash_id": "8sNkBoRFC4",
                    "name": "內衣【棉花樂芬妮】短膠骨軟鋼圈棉花杯舒適成套內衣",
                    "term_id": null
                    }
                ]
            }
        }';
        $response = json_decode($response, true);

        $data = (new CHYP\Partner\Echooss\Voucher\Type\Response('queryRedeemBatchDetail', $response['data'] ?? []))->format();

        $this->assertTrue($data->success);
        $this->assertEquals($data->details[0]->termId, 'D9341_A600');
    }

    /**
     * Ensure freeze redeem batch response works.
     */
    public function testFreezeRedeemBatch()
    {
        $response = '{
            "data": {
            "success": true,
            "message": "batch_id = 20220926-KgI8NHw has been frozen",
            "batch_freeze": 1
            }
        }';

        $response = json_decode($response, true);

        $data = (new CHYP\Partner\Echooss\Voucher\Type\Response('freezeRedeemBatch', $response['data'] ?? []))->format();

        $this->assertTrue($data->success);
        $this->assertEquals($data->batchFreeze, 1);
    }

    /**
     * Ensure update redeem batch response works.
     */
    public function testUpdateRedeemBatch()
    {
        $response = '{
            "data": {
                "success": true,
                "message": "Successfully updated : redeem details"
            }
        }';

        $response = json_decode($response, true);

        $data = (new CHYP\Partner\Echooss\Voucher\Type\Response('freezeRedeemBatch', $response['data'] ?? []))->format();

        $this->assertTrue($data->success);
        $this->assertEquals($data->message, 'Successfully updated : redeem details');
    }

    /**
     * Ensure execute redeem batch response works.
     */
    public function testExecuteRedeemBatch()
    {
        $response = '{
            "data": {
                "success": true,
                "message": "Successfully executed batch redeem"
            }
        }';

        $response = json_decode($response, true);

        $data = (new CHYP\Partner\Echooss\Voucher\Type\Response('executeRedeemBatch', $response['data'] ?? []))->format();

        $this->assertTrue($data->success);
        $this->assertEquals($data->message, 'Successfully executed batch redeem');
    }

    /**
     * Ensure reverse redeem response works.
     */
    public function testReverseRedeem()
    {
        $response = '{
            "data": {
                "success": true,
                "message": "Successfully reverse redeem by pos"
            }
        }';

        $response = json_decode($response, true);

        $data = (new CHYP\Partner\Echooss\Voucher\Type\Response('reverseRedeem', $response['data'] ?? []))->format();

        $this->assertTrue($data->success);
        $this->assertEquals($data->message, 'Successfully reverse redeem by pos');
    }

    /**
     * Ensure rewards card accumulate response works.
     */
    public function testRewardsCardAccumulatePointResponse()
    {
        $faker = Faker\Factory::create();
        $message = $faker->text(10);
        $point = 3;
        $amount = 300;

        $response = ['message' => $message, 'point' => $point, 'amount' => $amount];
        $data = (new CHYP\Partner\Echooss\Voucher\Type\Response('accumulatePoint', $response))->format();

        $this->assertEquals($data->message, $message);
        $this->assertEquals($data->point, $point);
        $this->assertEquals($data->amount, $amount);
    }

    /**
     * Ensure rewards card deplete response works.
     */
    public function testRewardsCardDepletePointResponse()
    {
        $faker = Faker\Factory::create();
        $message = $faker->text(10);

        $response = ['message' => $message];
        $data = (new CHYP\Partner\Echooss\Voucher\Type\Response('depletePoint', $response))->format();

        $this->assertEquals($data->message, $message);
    }
}
