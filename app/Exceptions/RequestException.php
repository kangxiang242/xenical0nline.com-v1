<?php

namespace App\Exceptions;

use Exception;

use Illuminate\Http\Exceptions\HttpResponseException;
class RequestException extends Exception
{
    public function __construct($message = "", $code = 0)
    {


        parent::__construct($message, $code);
    }
}
