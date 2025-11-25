<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Response;

use CHYP\Partner\Echooss\Voucher\Exception\ResponseTypeException;
use DateTimeImmutable;

class Voucher extends Response
{
    /**
     * Voucher hash id.
     *
     * @var string
     */
    public string $voucherHashId;

    /**
     * Voucher name.
     *
     * @var string
     */
    public string $name;

    /**
     * Voucher total quantity.
     *
     * @var int
     */
    public int $totalCount;

    /**
     * Remaining unused quantity.
     *
     * @var int
     */
    public int $unusableCount;

    /**
     * Redeemable quantity.
     *
     * @var int
     */
    public int $redeemableCount;

    /**
     * Reversible redeemable quantity.
     *
     * @var int
     */
    public int $reverseRedeemableCount;

    /**
     * Voidable quantity.
     *
     * @var int
     */
    public int $voidableCount;

    /**
     * Reversible voidable quantity.
     *
     * @var int
     */
    public int $reverseVoidableCount;

    /**
     * Member's Mobile Number.
     *
     * @var string
     */
    public string $phoneNumber;

    /**
     * Fields Visible Only for Member Coupons.
     *
     * Type 0 represents coupon.
     * Type 1 represents Membership Welcome Voucher.
     * Type 2 represents Birthday Gift Voucher.
     * Type 3 represents Joining Bonus Points.
     * Type 4 represents Birthday Bonus Points.
     * Type 5 represents Gift Package Bundle.
     *
     * @var int
     */
    public int $couponType;

    /**
     * Fields Visible Only for Member Vouchers.
     * Product Sales Period.
     *
     * 1 represents No Expiry.
     * 2 represents Specified Period.
     *
     *
     * @var int
     */
    public int $periodSales;

    /**
     * Fields Visible Only for Member Vouchers.
     *
     * Member Voucher Redemption Code.
     *
     * @var string
     */
    public ?string $termId;

    /**
     * Vocuher Type.
     *
     * Type 1 is coupon.
     * Type 2 is voucher.
     *
     * @param integer $value Voucher type value.
     *
     * @return integer
     */
    public function type(int $value): int
    {
        if ($value != 1 && $value != 2) {
            throw new ResponseTypeException('Voucher type is error.');
        }

        return $value;
    }

    /**
     * Build voucher image objects.
     *
     * @param array $images Image rows returned by API.
     *
     * @return array<\CHYP\Partner\Echooss\Voucher\Type\Response\VoucherImage>
     */
    public function images(array $images): array
    {
        $data = [];

        foreach ($images as $image) {
            $data[] = new VoucherImage($image['id'], $image['url'], $image['order']);
        }

        return $data;
    }

    /**
     * Convert raw date string to immutable date object.
     *
     * @param string|null $date Date string or null.
     *
     * @return \DateTimeImmutable|null Parsed date instance.
     */
    protected function processDate(?string $date): ?DateTimeImmutable
    {
        return !empty($date) ? new DateTimeImmutable($date) : null;
    }

    /**
     * Parse voucher start date.
     *
     * @param string|null $date Date string.
     *
     * @return \DateTimeImmutable|null Parsed date instance.
     */
    public function startDate(?string $date)
    {
        return $this->processDate($date);
    }

    /**
     * Parse voucher end date.
     *
     * @param string|null $date Date string.
     *
     * @return \DateTimeImmutable|null Parsed date instance.
     */
    public function endDate(?string $date)
    {
        return $this->processDate($date);
    }

    /**
     * Parse sales start date.
     *
     * @param string|null $date Date string.
     *
     * @return \DateTimeImmutable|null Parsed date instance.
     */
    public function salesStartDate(?string $date)
    {
        return $this->processDate($date);
    }

    /**
     * Parse sales end date.
     *
     * @param string|null $date Date string.
     *
     * @return \DateTimeImmutable|null
     */
    public function salesEndDate(?string $date)
    {
        return $this->processDate($date);
    }
}
