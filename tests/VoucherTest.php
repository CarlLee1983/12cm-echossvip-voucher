<?php

use CHYP\Partner\Echooss\Voucher\Core;
use CHYP\Partner\Echooss\Voucher\Exception\RequestTypeException;
use CHYP\Partner\Echooss\Voucher\Exception\ResponseTypeException;
use CHYP\Partner\Echooss\Voucher\Type\Request\CreateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\ExecuteRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\FreezeRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\QueryRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\QueryRedeemBatchDetail;
use CHYP\Partner\Echooss\Voucher\Type\Request\Redeem;
use CHYP\Partner\Echooss\Voucher\Type\Request\ReverseRedeem;
use CHYP\Partner\Echooss\Voucher\Type\Request\UpdateRedeemBatch;
use CHYP\Partner\Echooss\Voucher\Type\Request\VoucherList;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class VoucherTest extends TestCase
{
    /**
     * Call this template method before each test method is run.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->safeLoad();

        $this->core = new Core(true);

        $this->core->setToken($_ENV['VOUCHER_TOKEN']);
    }

    public function testApiUnauth()
    {
        $this->core->setToken('');

        $param = new VoucherList;
        $param->phoneNumber = '0912123456';

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionCode('401');

        $this->core->voucher('voucherList', $param);
    }

    public function testRequestVoucherListUserNotFoundByLineId()
    {
        # line
        $param = new VoucherList;
        $param->lineId = 'aaaaa';

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage('{"message":"user not found"}');
        $this->expectExceptionCode('422');

        $this->core->voucher('voucherList', $param);
    }

    public function testRequestVoucherListUserNotFoundByPhoneNumber()
    {
        $param = new VoucherList;
        $param->phoneNumber = '0912123456';

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage('{"message":"user not found"}');
        $this->expectExceptionCode('422');

        $this->core->voucher('voucherList', $param);
    }

    /**
     * @dataProvider additionCreateRedeemBatchProvider
     */
    public function testRequestCreateRedeemBatch($message, $param)
    {
        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage($message);

        $this->core->voucher('createRedeemBatch', $param);
    }

    public function additionCreateRedeemBatchProvider()
    {
        $param = new CreateRedeemBatch;
        $param->phoneNumber = '0912123456';
        $param->storeOpenId = '1111';
        $param->posMacUid = '22222';
        $param->batchList = [
            (new Redeem(1, 'aaa', 1))->toArray(),
        ];

        return [
            ['{"message":"Parameters Error","errors":"The selected store open id is invalid."}', $param],
        ];
    }

    public function testRequestQueryRedeemBatch()
    {
        $faker = Faker\Factory::create();

        $param = new QueryRedeemBatch;
        $param->batchToken = $faker->text(10);
        $param->storeOpenId = $faker->text(10);
        $param->posMacUid = $faker->text(10);

        $response = $this->core->voucher('queryRedeemBatch', $param);
        $data = $response->format();

        $this->assertTrue($data->success);
        $this->assertEquals($data->batchUuid, $param->batchToken);
    }

    public function testRequestQueryRedeemBatchDetail()
    {
        $faker = Faker\Factory::create();

        $param = new QueryRedeemBatchDetail;
        $param->batchUuid = $faker->text(10);
        $param->storeOpenId = $faker->text(10);
        $param->posMacUid = $faker->text(10);

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage('{"data":{"success":false,"message":"The batch_uuid not found"}}');

        $this->core->voucher('queryRedeemBatchDetail', $param);
    }

    public function testRequestFreezeRedeemBatchOverMins()
    {
        $param = new FreezeRedeemBatch;

        $this->expectException(RequestTypeException::class);
        $this->expectExceptionMessage('The freezeMins must be less than or equal to 60');

        $param->freezeMins = 80;
    }

    public function testRequestFreezeRedeemBatchLessMins()
    {
        $param = new FreezeRedeemBatch;

        $this->expectException(RequestTypeException::class);
        $this->expectExceptionMessage('The freezeMins must be less than or equal to 60');

        $param->freezeMins = 0;
    }

    public function testRequestFreezeRedeemBatch()
    {
        $faker = Faker\Factory::create();

        $param = new FreezeRedeemBatch;
        $param->batchUuid = $faker->text(10);
        $param->storeOpenId = $faker->text(10);
        $param->posMacUid = $faker->text(10);
        $param->freezeMins = 10;

        $response = $this->core->voucher('freezeRedeemBatch', $param);
        $response = $response->format();

        $this->assertTrue($response->success);
    }

    public function testUpdateRedeemBatch()
    {
        $faker = Faker\Factory::create();

        $param = new UpdateRedeemBatch;
        $param->batchUuid = 'ee11';
        $param->storeOpenId = $faker->text(10);
        $param->posMacUid = $faker->text(10);
        $param->batchList = [
            (new Redeem(1, 'abcd123', 1))->toArray(),
        ];

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage('{"data":{"success":false,"message":"not support for voucher ' . $param->batchUuid . '"}}');

        $this->core->voucher('updateRedeemBatch', $param);
    }

    public function testExecuteRedeemBatch()
    {
        $faker = Faker\Factory::create();

        $param = new ExecuteRedeemBatch;
        $param->batchUuid = $faker->text(10);
        $param->storeOpenId = $faker->text(10);
        $param->posMacUid = $faker->text(10);

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage('{"data":{"success":false,"message":"The batch_uuid not found"}}');

        $this->core->voucher('queryRedeemBatchDetail', $param);
    }

    /**
     * @dataProvider additionReverseRedeemProvider
     */
    public function testReverseRedeem($param, $message)
    {
        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage($message);

        $this->core->voucher('reverseRedeem', $param);

    }

    public function additionReverseRedeemProvider()
    {
        $provider = [];
        $faker = Faker\Factory::create();

        $param = new ReverseRedeem;
        $param->lineId = $faker->text(10);
        $param->type = 1;
        $param->voucherHashId = $faker->text(10);
        $param->deductCount = 1;

        $provider[] = [
            $param, '{"data":{"message":"The selected data.0.line_id is invalid."}}',
        ];

        $param = new ReverseRedeem;
        $param->type = 1;
        $param->voucherHashId = $faker->text(10);
        $param->deductCount = 1;

        $provider[] = [
            $param, '{"message":"Parameters Error","errors":"Thie line_id or phone_number field is required"}',
        ];

        $param = new ReverseRedeem;
        $param->type = 3;
        $param->voucherHashId = $faker->text(10);
        $param->deductCount = 1;

        $provider[] = [
            $param, '{"data":{"success":false,"message":"Product not found"}}',
        ];

        return $provider;
    }
}
