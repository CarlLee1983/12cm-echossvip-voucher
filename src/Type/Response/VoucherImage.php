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
     * __construct.
     *
     * @param int    $id
     * @param string $url
     * @param string $order
     */
    public function __construct(int $id, string $url, ?int $order)
    {
        $this->id = $id;
        $this->url = $url;
        $this->order = $order;
    }
}
