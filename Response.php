<?php

namespace App\Core;

class Response
{
    public function setStatusCode(int $code)
    {
        // set status code
        http_response_code($code);
    }

    public function redirect($url)
    {
        header('Location: '.$url);
    }
}