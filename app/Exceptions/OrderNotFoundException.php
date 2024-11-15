<?php
namespace App\Exceptions;

use Exception;

class OrderNotFoundException extends Exception
{
    public function __construct($message = "Order not found")
    {
        parent::__construct($message);
    }
}
