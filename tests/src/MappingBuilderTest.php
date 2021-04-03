<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class MappingBuilderTest extends PHPUnit_Framework_TestCase
{
    public function testAddActionWithNull()
    {
        $b = new MappingBuilder();
        $action = $b->addAction('foo');
        $this->assertNull($action[_FUNCTION]);
    }

    public function testAddActionWithString()
    {
        $b = new MappingBuilder();
        $action = $b->addAction('foo', 'bar');
        $this->assertEquals(
            'bar',
            $action[_FUNCTION]
        );
    }

    public function testAddActionWithCallable()
    {
        $b = new MappingBuilder();
        $fakeClass = new FakeAction();
        $call = [
            $fakeClass,
            'fakeAction',
        ];
        $action = $b->addAction('foo', $call);
        $this->assertEquals(
            $call,
            $action[_FUNCTION]
        );
    }
}
