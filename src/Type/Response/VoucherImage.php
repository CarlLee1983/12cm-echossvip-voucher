<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Response;

class VoucherImage extends Response
{
    /**
     * Image ID.
     *
     * @var int
     */
    public int $id;

    /**
     * Image Link.
     *
     * @var string
     */
    public string $url;

    /**
     * Order of Images.
     *
     * @var int
     */
    public ?int $order;

    /**
     * Build voucher image value object.
     *
     * @param integer      $id    Image identifier.
     * @param string       $url   Image URL.
     * @param integer|null $order Image order (nullable).
     */
    public function __construct(int $id, string $url, ?int $order)
    {
        $this->id = $id;
        $this->url = $url;
        $this->order = $order;
    }
}
