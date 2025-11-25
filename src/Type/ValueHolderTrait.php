<?php

namespace CHYP\Partner\Echooss\Voucher\Type;

trait ValueHolderTrait
{
    /**
     * Protected $data.
     *
     * @var array
     */
    protected array $protectedData = [];

    /**
     * Triggered when assigning to inaccessible properties.
     *
     * @param string                                         $varName Property name.
     * @param array|object|string|integer|float|boolean|null $value   Assigned value.
     *
     * @return void
     */
    public function __set(string $varName, $value)
    {
        if (method_exists($this, $varName)) {
            $this->protectedData[$varName] = $this->$varName($value);
        } else {
            $this->$varName = $value;
        }
    }

    /**
     * Triggered when reading inaccessible properties.
     *
     * @param string $varName Property name.
     *
     * @return mixed Stored value.
     */
    public function __get(string $varName)
    {
        return $this->protectedData[$varName] ?? $this->$varName;
    }
}
