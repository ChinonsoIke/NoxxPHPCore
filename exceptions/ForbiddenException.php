<?php

namespace NoxxPHP\Core\Exceptions;

use Exception;

class ForbiddenException extends Exception
{
    protected $code= 403;
    protected $message= "You don't have permission to access this page";
}