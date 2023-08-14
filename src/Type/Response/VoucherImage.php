<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Response;

class VoucherImage
{
    /**
     * Image ID.
     *
     * @var integer
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
     * @var integer
     */
    public ?int $order;

    /**
     * __construct
     *
     * @param int $id
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
