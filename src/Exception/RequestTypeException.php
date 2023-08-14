<?php

namespace CHYP\Partner\Echooss\Voucher\Exception;

use Exception;

class RequestTypeException extends Exception
{
    /**
     * __construct
     *
     * @param string $message
     * @param integer $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
