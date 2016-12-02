<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class ActionForwardTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        \PMVC\unplug('view');
        \PMVC\plug(
            'view', [
            _CLASS => '\PMVC\FakeView',
            ]
        );
    }

    public function testGet()
    {
        $fakeForward = [
            _PATH   => '',
            _HEADER => '',
            _TYPE   => 'view',
            _ACTION => '',
        ];
        $forward = new ActionForward($fakeForward);
        $fakeData = ['data' => 'abc'];
        $forward->set($fakeData);
        $get = $forward->get();
        $this->assertEquals(
            $fakeData,
            $get
        );
    }
}
