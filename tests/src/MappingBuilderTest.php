<?php

namespace PMVC;

class MappingBuilderTest extends TestCase
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

    public function testAddDuplicateAction()
    {
        $b = new MappingBuilder();
        $b->addAction('foo');
        @$b->addAction('foo');
        $this->assertTrue(isset($b[ACTION_MAPPINGS]['foo']));
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
