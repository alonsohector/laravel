<?php

namespace App\Exceptions;

use Exception;

class PostNotFoundException extends Exception
{
        /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report()
    {
        \Log::debug('Incfile - LOG: '.'POST error handle or does\'t exist.');
    }  
}
