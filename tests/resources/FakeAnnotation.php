<?php

namespace PMVC;

class FakeDocBlock
{
    public function getfile()
    {
        return '/phpunit/fake';
    }

    public function getStartLine()
    {
        return 0;
    }
}

class FakeAnnotation extends PlugIn
{
    public $attrs = ['obj' => []];

    public function getAttrs($func)
    {
        return $this->attrs;
    }

    public function get($func)
    {
        return new FakeDocBlock();
    }
}

class FakeSupervisorPlugin extends PlugIn
{
    public $processCallCount = 0;

    public function process()
    {
        $this->processCallCount++;
    }
}
