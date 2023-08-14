<?php

use CHYP\Partner\Echooss\Voucher\Type\Request\CreateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\ExecuteRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\FreezeRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\QueryRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\QueryRedeemBatchDetail;
use CHYP\Partner\Echooss\Voucher\Type\Request\ReverseRedeem;
use CHYP\Partner\Echooss\Voucher\Type\Request\UpdateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\VoucherList;
use PHPUnit\Framework\TestCase;

class RequestTypeTest extends TestCase
{
    public function testRequestVoucherListType()
    {
        $faker = Faker\Factory::create();
        $randomString = $faker->text(10);

        $data = new VoucherList;
        $data->lineId = $randomString;
        $this->assertEquals($data->lineId, $randomString);
    }

    public function testRequestVoucherListTypeToParams()
    {
        $data = new VoucherList;
        $faker = Faker\Factory::create();
        $data->lineId = $faker->text(10);

        $this->assertTrue(isset($data->toArray()['line_id']));
        $this->assertFalse(isset($data->toArray()['phone_number']));

        $data = new VoucherList;
        $phoneNumber = '0912123456';
        $data->phoneNumber = $phoneNumber;

        $this->assertFalse(isset($data->toArray()['line_id']));
        $this->assertTrue(isset($data->toArray()['phone_number']));
    }

    public function testRequestVoucherListTypeWithValidPhoneNumber()
    {
        $data = new VoucherList;
        $phoneNumber = '0912123456';
        $data->phoneNumber = $phoneNumber;

        $this->assertEquals($data->phoneNumber, $phoneNumber);
    }

    public function testRequestVoucherListTypeWithInvalidPhoneNumber()
    {
        $this->expectException(\CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException::class);
        $this->expectExceptionMessage('Invalid Taiwan mobile number.');

        $data = new VoucherList;
        $data->phoneNumber = '123';
    }

    public function testPhoneNumberIsSetCorrectly()
    {
        $data = new CreateRedeemBatch;
        $phoneNumber = '0912123456';
        $data->phoneNumber = $phoneNumber;

        $this->assertEquals($data->phoneNumber, $phoneNumber);
    }

    public function testStoreOpenIdAndPosMacUidAreSetCorrectly()
    {
        $data = new CreateRedeemBatch;
        $storeOpenId = 'adfasd';
        $posMacUid = 'bacafgds';

        $data->storeOpenId = $storeOpenId;
        $data->posMacUid = $posMacUid;

        $this->assertEquals($data->storeOpenId, $storeOpenId);
        $this->assertEquals($data->posMacUid, $posMacUid);

        $data = new CreateRedeemBatch;
        $params = $data->toArray();

        $this->assertEquals($params, [
            'store_open_id' => '',
            'pos_mac_uid' => '',
            'batch_list' => [],
            'phone_number' => '',
        ]);
    }

    public function testBatchListIsSetCorrectly()
    {
        $data = new CreateRedeemBatch;
        $faker = Faker\Factory::create();
        $id1 = $faker->text(10);
        $id2 = $faker->text(10);

        $data->batchList = [
            new CHYP\Partner\Echooss\Voucher\Type\Request\Redeem(1, $id1, 1),
            new CHYP\Partner\Echooss\Voucher\Type\Request\Redeem(2, $id2, 1),
        ];

        $this->assertIsArray($data->batchList);
        $this->assertCount(2, $data->batchList);

        $this->assertEquals($data->batchList[0]->redeemId, $id1);
        $this->assertEquals($data->batchList[1]->redeemId, $id2);
    }

    public function testQueryRedeemBatch()
    {
        $data = new QueryRedeemBatch;
        $params = $data->toArray();

        $this->assertEquals($params, [
            'batch_token' => '',
            'store_open_id' => '',
            'pos_mac_uid' => '',
        ]);

        $faker = Faker\Factory::create();

        $batchToken = $faker->text(10);
        $storeOpenId = $faker->text(10);
        $posMacUid = $faker->text(10);

        $data->batchToken = $batchToken;
        $data->storeOpenId = $storeOpenId;
        $data->posMacUid = $posMacUid;

        $params = $data->toArray();

        $this->assertEquals($params, [
            'batch_token' => $batchToken,
            'store_open_id' => $storeOpenId,
            'pos_mac_uid' => $posMacUid,
        ]);
    }

    public function testQueryRedeemBatchDetail()
    {
        $data = new QueryRedeemBatchDetail;
        $params = $data->toArray();

        $this->assertEquals($params, [
            'batch_uuid' => '',
            'store_open_id' => '',
            'pos_mac_uid' => '',
        ]);

        $faker = Faker\Factory::create();

        $batchUuid = $faker->text(10);
        $storeOpenId = $faker->text(10);
        $posMacUid = $faker->text(10);

        $data->batchUuid = $batchUuid;
        $data->storeOpenId = $storeOpenId;
        $data->posMacUid = $posMacUid;

        $params = $data->toArray();

        $this->assertEquals($params, [
            'batch_uuid' => $batchUuid,
            'store_open_id' => $storeOpenId,
            'pos_mac_uid' => $posMacUid,
        ]);
    }

    public function testFreezeRedeemBatch()
    {
        $data = new FreezeRedeemBatch;
        $params = $data->toArray();

        $this->assertEquals($params, [
            'batch_uuid' => '',
            'store_open_id' => '',
            'pos_mac_uid' => '',
            'freeze_mins' => 1,
        ]);

        $faker = Faker\Factory::create();

        $batchUuid = $faker->text(10);
        $storeOpenId = $faker->text(10);
        $posMacUid = $faker->text(10);

        $data->batchUuid = $batchUuid;
        $data->storeOpenId = $storeOpenId;
        $data->posMacUid = $posMacUid;
        $data->freezeMins = 3;

        $params = $data->toArray();

        $this->assertEquals($params, [
            'batch_uuid' => $batchUuid,
            'store_open_id' => $storeOpenId,
            'pos_mac_uid' => $posMacUid,
            'freeze_mins' => 3,
        ]);
    }

    public function testUpdateRedeemBatch()
    {
        $data = new UpdateRedeemBatch;
        $faker = Faker\Factory::create();
        $id1 = $faker->text(10);
        $id2 = $faker->text(10);

        $data->batchList = [
            new CHYP\Partner\Echooss\Voucher\Type\Request\Redeem(1, $id1, 1),
            new CHYP\Partner\Echooss\Voucher\Type\Request\Redeem(2, $id2, 1),
        ];

        $this->assertIsArray($data->batchList);
        $this->assertCount(2, $data->batchList);

        $this->assertEquals($data->batchList[0]->redeemId, $id1);
        $this->assertEquals($data->batchList[1]->redeemId, $id2);
    }

    public function testExecuteRedeemBatch()
    {
        $data = new ExecuteRedeemBatch;
        $params = $data->toArray();

        $this->assertEquals($params, [
            'batch_uuid' => '',
            'store_open_id' => '',
            'pos_mac_uid' => '',
        ]);

        $faker = Faker\Factory::create();

        $batchUuid = $faker->text(10);
        $storeOpenId = $faker->text(10);
        $posMacUid = $faker->text(10);

        $data->batchUuid = $batchUuid;
        $data->storeOpenId = $storeOpenId;
        $data->posMacUid = $posMacUid;

        $params = $data->toArray();

        $this->assertEquals($params, [
            'batch_uuid' => $batchUuid,
            'store_open_id' => $storeOpenId,
            'pos_mac_uid' => $posMacUid,
        ]);
    }

    public function testReverseRedeem()
    {
        $data = new ReverseRedeem;
        $phoneNumber = '0912123456';
        $data->phoneNumber = $phoneNumber;

        $this->assertEquals($data->phoneNumber, $phoneNumber);
    }
}
