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

    public function testAppendConfigToRunApp()
    {
        $run = plug(_RUN_APP, [
            _CLASS=> '\PMVC\FakePlugIn',
        ]);
        $fakeForward = [
            _PATH   => '',
            _HEADER => '',
            _TYPE   => 'action',
            _ACTION => '',
        ];
        $forward = new ActionForward($fakeForward);
        $utKey = 'data';
        $expected = 'xxx';
        $forward->set($utKey, $expected);
        $forward->go();
        $this->assertEquals(
            $expected,
            $run[$utKey]
        );
    }

    public function testAppendView()
    {
        $mock = $this->getMockBuilder('\PMVC\FakeView')
            ->setMethods(['append'])
            ->getMock();
        $mock->expects($this->exactly(1))
            ->method('append');
        \PMVC\replug('view', $mock);
        $fakeForward = [
            _PATH   => '',
            _HEADER => '',
            _TYPE   => 'view',
            _ACTION => '',
        ];
        $forward = new ActionForward($fakeForward);
        $forward->append(['foo'=>'bar']);
    }
}
