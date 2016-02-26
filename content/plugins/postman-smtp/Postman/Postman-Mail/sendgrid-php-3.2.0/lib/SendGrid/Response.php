<?php

namespace SendGrid;

class Response
{
    public
        $code,
        $headers,
        $raw_body,
        $body;

    public function __construct($code, $headers, $raw_body, $body)
    {
        $this->code = $code;
        $this->headers = $headers;
        $this->raw_body = $raw_body;
        $this->body = $body;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getRawBody()
    {
        return $this->raw_body;
    }

    public function getBody()
    {
        return $this->body;
    }
}
