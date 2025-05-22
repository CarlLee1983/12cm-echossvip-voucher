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

/**
 * Test suite for Voucher functionality.
 *
 * These tests interact with the live API (sandbox) and require .env configuration
 * for VOUCHER_TOKEN. They cover various voucher API actions, primarily testing
 * expected error responses due to invalid or dummy data.
 */
class VoucherTest extends TestCase
{
    /**
     * The Core service instance configured for Voucher operations.
     *
     * @var \CHYP\Partner\Echooss\Voucher\Core
     */
    protected Core $core;

    /**
     * Sets up the test environment before each test.
     *
     * Initializes Dotenv to load environment variables and configures the Core service
     * for voucher operations in sandbox mode with the appropriate token.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->safeLoad();

        $this->core = (new Core(true, 'voucher'))
            ->setToken($_ENV['VOUCHER_TOKEN']);
    }

    /**
     * Tests API unauthorized access when no token is provided.
     * It expects a ResponseTypeException with a 401 status code.
     */
    public function testApiUnauth(): void
    {
        $this->core->setToken('');

        $param = new VoucherList();
        $param->phoneNumber = '0912123456'; // Dummy phone number

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionCode('401');

        $this->core->voucher('voucherList', $param);
    }

    /**
     * Tests 'voucherList' action with a non-existent lineId.
     * Expects a ResponseTypeException indicating user not found (422).
     */
    public function testRequestVoucherListUserNotFoundByLineId(): void
    {
        $param = new VoucherList();
        $param->lineId = 'nonExistentLineId123';

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage('{"message":"user not found"}');
        $this->expectExceptionCode('422');

        $this->core->voucher('voucherList', $param);
    }

    /**
     * Tests 'voucherList' action with a non-existent phoneNumber.
     * Expects a ResponseTypeException indicating user not found (422).
     */
    public function testRequestVoucherListUserNotFoundByPhoneNumber(): void
    {
        $param = new VoucherList();
        $param->phoneNumber = '0900000000'; // Dummy phone number

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage('{"message":"user not found"}');
        $this->expectExceptionCode('422');

        $this->core->voucher('voucherList', $param);
    }

    /**
     * Tests 'createRedeemBatch' action with various invalid parameters.
     *
     * @dataProvider additionCreateRedeemBatchProvider
     * @param string $message The expected exception message.
     * @param \CHYP\Partner\Echooss\Voucher\Type\Request\CreateRedeemBatch $param The request parameters.
     */
    public function testRequestCreateRedeemBatch(string $message, CreateRedeemBatch $param): void
    {
        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage($message);

        $this->core->voucher('createRedeemBatch', $param);
    }

    /**
     * Data provider for testRequestCreateRedeemBatch.
     * Provides test cases with invalid parameters for creating a redeem batch.
     *
     * @return array<string, array{0: string, 1: \CHYP\Partner\Echooss\Voucher\Type\Request\CreateRedeemBatch}>
     */
    public function additionCreateRedeemBatchProvider(): array
    {
        $param = new CreateRedeemBatch();
        $param->phoneNumber = '0912123456'; // Dummy phone number
        $param->storeOpenId = 'invalidStoreId';
        $param->posMacUid = 'invalidPosMacUid';
        $param->batchList = [
            (new Redeem(1, 'invalidRedeemId', 1))->toArray(),
        ];

        return [
            'Invalid store_open_id' => ['{"message":"Parameters Error","errors":"The selected store open id is invalid."}', $param],
        ];
    }

    /**
     * Tests 'queryRedeemBatch' action with a dummy batchToken.
     * Expects a ResponseTypeException indicating failure to query.
     */
    public function testRequestQueryRedeemBatch(): void
    {
        $faker = \Faker\Factory::create();

        $param = new QueryRedeemBatch();
        $param->batchToken = $faker->text(10);
        $param->storeOpenId = $faker->text(10);
        $param->posMacUid = $faker->text(10);

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage('{"data":{"success":false,"message":"Fail to query : batch_uuid"}}');

        $this->core->voucher('queryRedeemBatch', $param);
    }

    /**
     * Tests 'queryRedeemBatchDetail' action with a dummy batchUuid.
     * Expects a ResponseTypeException indicating batch_uuid not found.
     */
    public function testRequestQueryRedeemBatchDetail(): void
    {
        $faker = \Faker\Factory::create();

        $param = new QueryRedeemBatchDetail();
        $param->batchUuid = $faker->uuid();
        $param->storeOpenId = $faker->text(10);
        $param->posMacUid = $faker->text(10);

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage('{"data":{"success":false,"message":"The batch_uuid not found"}}');

        $this->core->voucher('queryRedeemBatchDetail', $param);
    }

    /**
     * Tests 'freezeRedeemBatch' action with freezeMins exceeding the maximum limit.
     * Expects a RequestTypeException.
     */
    public function testRequestFreezeRedeemBatchOverMins(): void
    {
        $param = new FreezeRedeemBatch();

        $this->expectException(RequestTypeException::class);
        $this->expectExceptionMessage('The freezeMins must be less than or equal to 60');

        $param->freezeMins = 80;
    }

    /**
     * Tests 'freezeRedeemBatch' action with freezeMins below the minimum limit.
     * Expects a RequestTypeException.
     * Note: The current validation message is for "less than or equal to 60",
     * which might not cover "greater than or equal to 1" implicitly.
     */
    public function testRequestFreezeRedeemBatchLessMins(): void
    {
        $param = new FreezeRedeemBatch();

        $this->expectException(RequestTypeException::class);
        // The message "The freezeMins must be less than or equal to 60" is from the existing code.
        // A more accurate test for minimum might expect "must be greater than or equal to 1".
        $this->expectExceptionMessage('The freezeMins must be less than or equal to 60');


        $param->freezeMins = 0;
    }

    /**
     * Tests 'freezeRedeemBatch' action with a dummy batchUuid.
     * Expects a ResponseTypeException indicating batch_uuid not found.
     */
    public function testRequestFreezeRedeemBatch(): void
    {
        $faker = \Faker\Factory::create();

        $param = new FreezeRedeemBatch();
        $param->batchUuid = $faker->uuid();
        $param->storeOpenId = $faker->text(10);
        $param->posMacUid = $faker->text(10);
        $param->freezeMins = 10;

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage('{"data":{"success":false,"message":"batch_uuid not found"}}');

        $this->core->voucher('freezeRedeemBatch', $param);
    }

    /**
     * Tests 'updateRedeemBatch' action with a dummy batchUuid.
     * Expects a ResponseTypeException indicating the voucher is not supported for update.
     */
    public function testUpdateRedeemBatch(): void
    {
        $faker = \Faker\Factory::create();

        $param = new UpdateRedeemBatch();
        $param->batchUuid = 'nonExistentOrUnsupportedUuid';
        $param->storeOpenId = $faker->text(10);
        $param->posMacUid = $faker->text(10);
        $param->batchList = [
            (new Redeem(1, 'dummyRedeemId123', 1))->toArray(),
        ];

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage('{"data":{"success":false,"message":"not support for voucher ' . $param->batchUuid . '"}}');

        $this->core->voucher('updateRedeemBatch', $param);
    }

    /**
     * Tests 'executeRedeemBatch' action with a dummy batchUuid.
     * Expects a ResponseTypeException indicating batch_uuid not found.
     */
    public function testExecuteRedeemBatch(): void
    {
        $faker = \Faker\Factory::create();

        $param = new ExecuteRedeemBatch();
        $param->batchUuid = $faker->uuid();
        $param->storeOpenId = $faker->text(10);
        $param->posMacUid = $faker->text(10);

        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage('{"data":{"success":false,"message":"batch_uuid not found"}}');

        $this->core->voucher('executeRedeemBatch', $param);
    }

    /**
     * Tests 'reverseRedeem' action with various invalid parameters.
     *
     * @dataProvider additionReverseRedeemProvider
     * @param \CHYP\Partner\Echooss\Voucher\Type\Request\ReverseRedeem $param The request parameters.
     * @param string $message The expected exception message.
     */
    public function testReverseRedeem(ReverseRedeem $param, string $message): void
    {
        $this->expectException(ResponseTypeException::class);
        $this->expectExceptionMessage($message);

        $this->core->voucher('reverseRedeem', $param);
    }

    /**
     * Data provider for testReverseRedeem.
     * Provides test cases with invalid parameters for reversing a redeem action.
     *
     * @return array<string, array{0: \CHYP\Partner\Echooss\Voucher\Type\Request\ReverseRedeem, 1: string}>
     */
    public function additionReverseRedeemProvider(): array
    {
        $faker = \Faker\Factory::create();
        $provider = [];

        // Case 1: Invalid lineId
        $param1 = new ReverseRedeem();
        $param1->lineId = $faker->text(10); // Invalid lineId
        $param1->type = 1;
        $param1->voucherHashId = $faker->text(10);
        $param1->deductCount = 1;
        $provider['Invalid lineId'] = [
            $param1, '{"data":{"message":"The selected data.0.line_id is invalid."}}',
        ];

        // Case 2: Missing lineId or phoneNumber
        $param2 = new ReverseRedeem();
        // $param2->lineId or $param2->phoneNumber is missing
        $param2->type = 1;
        $param2->voucherHashId = $faker->text(10);
        $param2->deductCount = 1;
        $provider['Missing lineId or phoneNumber'] = [
            $param2, '{"message":"Parameters Error","errors":"Thie line_id or phone_number field is required"}',
        ];

        // Case 3: Product not found for type 3
        $param3 = new ReverseRedeem();
        $param3->phoneNumber = '0900000000'; // Dummy phone number
        $param3->type = 3; // Type that might relate to a product
        $param3->voucherHashId = $faker->text(10); // Non-existent voucher
        $param3->deductCount = 1;
        $provider['Product not found for type 3'] = [
            $param3, '{"data":{"success":false,"message":"Product not found"}}',
        ];

        return $provider;
    }
}
