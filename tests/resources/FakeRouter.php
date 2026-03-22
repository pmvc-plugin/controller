<?php

namespace PMVC;

class FakeRouter extends PlugIn
{
    public function buildCommand($url, $params)
    {
        return $url;
    }

    public function go($path, $redirect = false)
    {
    }

    public function processHeader($headers)
    {
    }
}
