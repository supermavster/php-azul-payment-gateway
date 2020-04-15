<?php

namespace Core;

use Exception;

class AzulPaymentException extends Exception
{

    protected $details;

    public function __construct(string $message = "", $details = null)
    {
        $this->details = $details;
        parent::__construct($message);
    }

    public function getDetails()
    {
        return $this->details;
    }
}
