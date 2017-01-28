<?php
namespace PSN;

class AuthException extends \Exception
{
    private $_error;

    public function __construct($json)
    {
        $this->_error = $json;
    }

    public function GetError() { return $this->_error; }
    public function GetErrorMessage() { return $this->_error->error_description; }
    public function GetErrorCode() { return $this->_error->error_code; }
}
