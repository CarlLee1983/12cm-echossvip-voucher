<?php

namespace CHYP\Partner\Echooss\Voucher\Exception;

use Exception;

class RequestTypeException extends Exception
{
    /**
     * @param string          $message  Error message.
     * @param integer         $code     Error code.
     * @param \Throwable|null $previous Previous throwable.
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
