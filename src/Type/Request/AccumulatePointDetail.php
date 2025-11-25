<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Request;

class AccumulatePointDetail extends Request
{
    /**
     * Product name.
     *
     * @var string
     */
    public string $productName = '';

    /**
     * Product unit price.
     *
     * @var int
     */
    public int $unitPrice = 0;

    /**
     * Quantity.
     *
     * @var int
     */
    public int $quantity = 1;

    /**
     * @param string  $productName Product name.
     * @param integer $unitPrice   Unit price.
     * @param integer $quantity    Quantity.
     */
    public function __construct(string $productName, int $unitPrice, int $quantity)
    {
        $this->productName = $productName;
        $this->unitPrice = $unitPrice;
        $this->quantity = $quantity;
    }
}
