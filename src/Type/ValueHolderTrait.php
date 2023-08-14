<?php

namespace CHYP\Partner\Echooss\Voucher\Type;

trait ValueHolderTrait
{
    /**
     * Protected $data
     *
     * @var array
     */
    protected array $protectedData = [];

    /**
     * Is invoked when writing a value to a non-existing or inaccessible property.
     *
     * @param string $varName
     * @param mixed $value
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
     * Is utilized for reading data from inaccessible (protected or private) or non-existing properties.
     *
     * @param string $varName
     *
     * @return mixed
     */
    public function __get(string $varName)
    {
        return $this->protectedData[$varName] ?? $this->$varName;
    }
}
