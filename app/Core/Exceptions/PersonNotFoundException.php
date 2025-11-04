<?php

namespace App\Core\Exceptions;

use Exception;

class PersonNotFoundException extends Exception
{
    public function __construct(string $identifier = 'Person')
    {
        parent::__construct("$identifier not found");
    }
}
