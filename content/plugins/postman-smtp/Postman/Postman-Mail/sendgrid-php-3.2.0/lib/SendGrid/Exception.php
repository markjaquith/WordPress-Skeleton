<?php

namespace SendGrid;

/**
 * An exception thrown when SendGrid does not return a 200
 */
class Exception extends \Exception
{
    public function getErrors()
    {
        return json_decode($this->message)->errors;
    }
}
