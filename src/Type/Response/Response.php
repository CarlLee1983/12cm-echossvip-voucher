<?php

namespace CHYP\Partner\Echooss\Voucher\Type\Response;

use CHYP\Partner\Echooss\Voucher\Type\ValueHolderTrait;

abstract class Response implements ResponseInterface
{
    use ValueHolderTrait;

    /**
     * Self name.
     *
     * @var string
     */
    protected string $className;

    /**
     * __construct.
     */
    public function __construct()
    {
    }

    /**
     * Object to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $declaredAssets = get_object_vars($this);

        unset($declaredAssets['protectedData']);

        return array_merge($declaredAssets, $this->protectedData);
    }
}
