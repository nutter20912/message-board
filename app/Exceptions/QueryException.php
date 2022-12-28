<?php

namespace App\Exceptions;

class QueryException extends \RuntimeException
{
    public function context()
    {
        return ['reason' => $this->getPrevious()->message];
    }
}
