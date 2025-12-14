<?php

namespace Tests;

use CHYP\Partner\Echooss\Voucher\Type\Request\AccumulatePoint;
use CHYP\Partner\Echooss\Voucher\Type\Request\AccumulatePointDetail;
use CHYP\Partner\Echooss\Voucher\Type\Request\CreateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\DepletePoint;
use CHYP\Partner\Echooss\Voucher\Type\Request\ExecuteRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\FreezeRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\QueryRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\QueryRedeemBatchDetail;
use CHYP\Partner\Echooss\Voucher\Type\Request\Redeem;
use CHYP\Partner\Echooss\Voucher\Type\Request\ReverseRedeem;
use CHYP\Partner\Echooss\Voucher\Type\Request\UpdateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\VoucherList;
use PHPUnit\Framework\TestCase;

class RequestTypeTest extends TestCase
{
    /**
     * Ensure voucher list accepts line ID.
     */
    public function testRequestVoucherListType()
    {
        $randomString = 'test_line_id_123';

        $data = new VoucherList();
        $data->lineId = $randomString;
        $this->assertEquals($data->lineId, $randomString);
    }

    /**
     * Ensure voucher list payload toggles between phone and line.
     */
    public function testRequestVoucherListTypeToParams()
    {
        $data = new VoucherList();
        $data->lineId = 'test_line_id_456';

        $this->assertTrue(isset($data->toArray()['line_id']));
        $this->assertFalse(isset($data->toArray()['phone_number']));

        $data = new VoucherList();
        $phoneNumber = '0912123456';
        $data->phoneNumber = $phoneNumber;

        $this->assertFalse(isset($data->toArray()['line_id']));
        $this->assertTrue(isset($data->toArray()['phone_number']));
    }

    /**
     * Ensure voucher list accepts valid phone number.
     */
    public function testRequestVoucherListTypeWithValidPhoneNumber()
    {
        $data = new VoucherList();
        $phoneNumber = '0912123456';
        $data->phoneNumber = $phoneNumber;

        $this->assertEquals($data->phoneNumber, $phoneNumber);
    }

    /**
     * Ensure invalid phone number throws exception.
     */
    public function testRequestVoucherListTypeWithInvalidPhoneNumber()
    {
        $this->expectException(\CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException::class);
        $this->expectExceptionMessage('Invalid Taiwan mobile number.');

        $data = new VoucherList();
        $data->phoneNumber = '123';
    }

    /**
     * Ensure phone number mutator stores value.
     */
    public function testPhoneNumberIsSetCorrectly()
    {
        $data = new CreateRedeemBatch();
        $phoneNumber = '0912123456';
        $data->phoneNumber = $phoneNumber;

        $this->assertEquals($data->phoneNumber, $phoneNumber);
    }

    /**
     * Ensure storeOpenId/posMacUid mutators store values and defaults.
     */
    public function testStoreOpenIdAndPosMacUidAreSetCorrectly()
    {
        $data = new CreateRedeemBatch();
        $storeOpenId = 'adfasd';
        $posMacUid = 'bacafgds';

        $data->storeOpenId = $storeOpenId;
        $data->posMacUid = $posMacUid;

        $this->assertEquals($data->storeOpenId, $storeOpenId);
        $this->assertEquals($data->posMacUid, $posMacUid);

        $data = new CreateRedeemBatch();
        $params = $data->toArray();

        $this->assertEquals($params, [
            'store_open_id' => '',
            'pos_mac_uid'   => '',
            'batch_list'    => [],
            'phone_number'  => '',
        ]);
    }

    /**
     * Ensure batch list stores Redeem DTOs.
     */
    public function testBatchListIsSetCorrectly()
    {
        $data = new CreateRedeemBatch();
        $id1 = 'voucher_1';
        $id2 = 'voucher_2';

        $data->batchList = [
            new Redeem(1, $id1, 1),
            new Redeem(2, $id2, 1),
        ];

        $this->assertIsArray($data->batchList);
        $this->assertCount(2, $data->batchList);

        $this->assertEquals($data->batchList[0]->redeemId, $id1);
        $this->assertEquals($data->batchList[1]->redeemId, $id2);
    }

    /**
     * Ensure query redeem batch toArray mapping works.
     */
    public function testQueryRedeemBatch()
    {
        $data = new QueryRedeemBatch();
        $params = $data->toArray();

        $this->assertEquals($params, [
            'batch_token'   => '',
            'store_open_id' => '',
            'pos_mac_uid'   => '',
        ]);

        $batchToken = 'token_abc';
        $storeOpenId = 'store_123';
        $posMacUid = 'pos_456';

        $data->batchToken = $batchToken;
        $data->storeOpenId = $storeOpenId;
        $data->posMacUid = $posMacUid;

        $params = $data->toArray();

        $this->assertEquals($params, [
            'batch_token'   => $batchToken,
            'store_open_id' => $storeOpenId,
            'pos_mac_uid'   => $posMacUid,
        ]);
    }

    /**
     * Ensure query redeem batch detail toArray mapping works.
     */
    public function testQueryRedeemBatchDetail()
    {
        $data = new QueryRedeemBatchDetail();
        $params = $data->toArray();

        $this->assertEquals($params, [
            'batch_uuid'    => '',
            'store_open_id' => '',
            'pos_mac_uid'   => '',
        ]);

        $batchUuid = 'uuid_789';
        $storeOpenId = 'store_123';
        $posMacUid = 'pos_456';

        $data->batchUuid = $batchUuid;
        $data->storeOpenId = $storeOpenId;
        $data->posMacUid = $posMacUid;

        $params = $data->toArray();

        $this->assertEquals($params, [
            'batch_uuid'    => $batchUuid,
            'store_open_id' => $storeOpenId,
            'pos_mac_uid'   => $posMacUid,
        ]);
    }

    /**
     * Ensure freeze redeem batch toArray mapping works.
     */
    public function testFreezeRedeemBatch()
    {
        $data = new FreezeRedeemBatch();
        $params = $data->toArray();

        $this->assertEquals($params, [
            'batch_uuid'    => '',
            'store_open_id' => '',
            'pos_mac_uid'   => '',
            'freeze_mins'   => 1,
        ]);

        $batchUuid = 'uuid_111';
        $storeOpenId = 'store_222';
        $posMacUid = 'pos_333';

        $data->batchUuid = $batchUuid;
        $data->storeOpenId = $storeOpenId;
        $data->posMacUid = $posMacUid;
        $data->freezeMins = 3;

        $params = $data->toArray();

        $this->assertEquals($params, [
            'batch_uuid'    => $batchUuid,
            'store_open_id' => $storeOpenId,
            'pos_mac_uid'   => $posMacUid,
            'freeze_mins'   => 3,
        ]);
    }

    /**
     * Ensure update redeem batch handles batch list.
     */
    public function testUpdateRedeemBatch()
    {
        $data = new UpdateRedeemBatch();
        $id1 = 'voucher_A';
        $id2 = 'voucher_B';

        $data->batchList = [
            new Redeem(1, $id1, 1),
            new Redeem(2, $id2, 1),
        ];

        $this->assertIsArray($data->batchList);
        $this->assertCount(2, $data->batchList);

        $this->assertEquals($data->batchList[0]->redeemId, $id1);
        $this->assertEquals($data->batchList[1]->redeemId, $id2);
    }

    /**
     * Ensure execute redeem batch toArray mapping works.
     */
    public function testExecuteRedeemBatch()
    {
        $data = new ExecuteRedeemBatch();
        $params = $data->toArray();

        $this->assertEquals($params, [
            'batch_uuid'    => '',
            'store_open_id' => '',
            'pos_mac_uid'   => '',
        ]);

        $batchUuid = 'uuid_exec';
        $storeOpenId = 'store_exec';
        $posMacUid = 'pos_exec';

        $data->batchUuid = $batchUuid;
        $data->storeOpenId = $storeOpenId;
        $data->posMacUid = $posMacUid;

        $params = $data->toArray();

        $this->assertEquals($params, [
            'batch_uuid'    => $batchUuid,
            'store_open_id' => $storeOpenId,
            'pos_mac_uid'   => $posMacUid,
        ]);
    }

    /**
     * Ensure reverse redeem stores phone number.
     */
    public function testReverseRedeem()
    {
        $data = new ReverseRedeem();
        $phoneNumber = '0912123456';
        $data->phoneNumber = $phoneNumber;

        $this->assertEquals($data->phoneNumber, $phoneNumber);
    }

    /**
     * Ensure accumulate point payload mapping works.
     */
    public function testAccumulatePoint()
    {
        $data = new AccumulatePoint();
        $phoneNumber = '0912123456';

        $data->phoneNumber = $phoneNumber;
        $data->amount = 10;
        $data->details = [
            (new AccumulatePointDetail('test', 10, 1))->toArray(),
        ];

        $params = $data->toArray();

        $this->assertEquals($params, [
            'phone_number' => $phoneNumber,
            'amount'       => $data->amount,
            'details'      => [
                [
                    'product_name' => 'test',
                    'unit_price'   => 10,
                    'quantity'     => 1,
                ],
            ],
        ]);
    }

    /**
     * Ensure deplete point payload mapping works.
     */
    public function testDepletePoint()
    {
        $data = new DepletePoint();
        $data->phoneNumber = '0912123456';
        $data->point = 10;

        $params = $data->toArray();

        $this->assertEquals($params, [
            'phone_number' => $data->phoneNumber,
            'point'        => $data->point,
        ]);
    }
}
