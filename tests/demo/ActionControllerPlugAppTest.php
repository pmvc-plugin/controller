<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class ActionControllerPlugAppTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        folders(_RUN_APP, [], [], true);
        $this->resources = __dir__.'/../resources/';
        unPlug(_RUN_APP);
    }

    public function testStore()
    {
        $folders = [
            $this->resources.'apps1',
            $this->resources.'apps2',
        ];
        $mvc = plug('controller');
        $mvc->setApp('testApp');
        $mvc->plugApp($folders);
        $store = folders(_RUN_APP);
        $expected = array_reverse($folders);
        $this->assertEquals(
            $expected,
            $store['folders']
        );
    }

    public function testPlugApp()
    {
        $folders = [
            $this->resources.'apps1',
            $this->resources.'apps2',
        ];
        $mvc = plug('controller');
        $mvc->setApp('testApp');
        $result = $mvc->plugApp($folders);
        $this->assertTrue($result);
        $this->assertEquals(
            'app2',
            getOption('test')
        );
    }

    public function testGetAppsParent()
    {
        $folders = [
            $this->resources.'apps1',
        ];
        $mvc = plug('controller');
        $mvc->setApp('testApp');
        $result = $mvc->plugApp($folders);
        $this->assertEquals(realpath($this->resources).'/', \PMVC\getAppsParent());
    }
}
