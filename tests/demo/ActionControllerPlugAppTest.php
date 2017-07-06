<?php

namespace PMVC;

use PHPUnit_Framework_TestCase;

class ActionControllerPlugAppTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        folders(_RUN_APP, [], [], true);
        $this->resources = __DIR__.'/../resources/';
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
        $expected = array_reverse(array_map(function ($d) {
            return realpath($d);
        }, $folders));
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
        $mvc = plug('controller');
        $mvc->setApp('testApp');
        $mvc->plugApp([$this->resources.'apps1']);
        $this->assertEquals(realpath($this->resources).'/', \PMVC\getAppsParent());
    }

    public function testTransparent()
    {
        $mvc = plug('controller');
        $mvc->setApp('testApp');
        $mvc->plugApp([$this->resources.'apps1']);
        $this->assertEquals(
            realpath($this->resources).'/FakeView.php',
            \PMVC\transparent('FakeView.php')
        );
        $this->assertEquals(
            realpath($this->resources).'/apps1/testApp/index.php',
            \PMVC\transparent('index.php')
        );
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetAppFail()
    {
        $mvc = plug('controller');
        $mvc->setApp('xxx');
        $mvc->plugApp();
    }

    /**
     * @expectedException DomainException
     */
    public function testSetDefaultAppFail()
    {
        $mvc = plug('controller');

        $mvc->setApp('xxx');
        $mvc[_DEFAULT_APP] = '';
        @$mvc->plugApp();
    }

    public function testMapRequestHaveRealApp()
    {
        unplug('another');
        $another = \PMVC\plug(
            'another', [
                _CLASS => '\PMVC\AnotherPlugin',
                'assert'=>_REAL_APP
            ]
        );
        $mvc = plug('controller');
        $mvc->setApp('testApp');
        $mvc->plugApp();
        $this->assertEquals(
            'testApp',
            $another['actual']
        );
    }
}
