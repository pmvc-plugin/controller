<?php

$b = new PMVC\MappingBuilder();
${_INIT_CONFIG} = [
    _CLASS        => __NAMESPACE__.'\FakeAction1',
    _INIT_BUILDER => $b,
];

class FakeAction1 extends \PMVC\Action
{
    public function init()
    {
        \PMVC\option('set', 'test', 'app1');
    }
}
