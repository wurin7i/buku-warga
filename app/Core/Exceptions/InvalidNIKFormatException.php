<?php

namespace App\Core\Exceptions;

use Exception;

class InvalidNIKFormatException extends Exception
{
    public function __construct(string $nik)
    {
        parent::__construct("Invalid NIK format: '$nik'. NIK must be 16 digits");
    }
}
