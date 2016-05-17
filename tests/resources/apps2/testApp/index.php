<?php

$b = new PMVC\MappingBuilder();
${_INIT_CONFIG} = [
    _CLASS        => __NAMESPACE__.'\FakeAction2',
    _INIT_BUILDER => $b,
];

class FakeAction2 extends \PMVC\Action
{
    public function init()
    {
        \PMVC\option('set', 'test', 'app2');
    }
}
