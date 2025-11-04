<?php

namespace App\Core\Exceptions;

use Exception;

class DuplicateNIKException extends Exception
{
    public function __construct(string $nik)
    {
        parent::__construct("NIK '$nik' already exists in the system");
    }
}
