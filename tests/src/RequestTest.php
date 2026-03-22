<?php

namespace PMVC;

class RequestTest extends TestCase
{
    public function testSetAndGetMethod()
    {
        $request = new Request();
        $request->setMethod('POST');
        $this->assertEquals('POST', $request->getMethod());
    }
}
