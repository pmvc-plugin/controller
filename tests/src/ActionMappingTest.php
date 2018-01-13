<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class ActionMappingTest extends PHPUnit_Framework_TestCase
{
    public function testActionMappingOffsetGet()
    {
        $unitPath = 'forward-path';
        $mappings = new ActionMappings();
        $builder = new MappingBuilder();
        $builder->addAction('action');
        $builder->addForward('forward', [
            _PATH=> $unitPath,
            _TYPE=> 'action',
        ]);
        $mappings->add($builder);
        $a = $mappings->findAction('action');
        $forward = $a->offsetGet('forward');
        $this->assertEquals($unitPath, $forward->getPath());
    }
}
