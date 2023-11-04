<?php

namespace AcumenLogger\Exceptions;

use Exception;

class AcumenEnvironmentVariablesNotSet extends Exception
{
    public function __construct()
    {
        $this->message = "Please set the ACUMEN_PROJECT_ID and ACUMEN_PROJECT_SECRET";
    }
}
