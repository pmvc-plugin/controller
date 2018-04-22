<?php

namespace PMVC;

use Exception;
use PHPUnit_Framework_Error;
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
        $this->assertEquals(realpath($this->resources).'/', $mvc->get_apps_parent());
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetAppFail()
    {
        try {
            $mvc = plug('controller');
            $mvc->setApp('xxx');
            $mvc->plugApp();
        } catch (Exception $e) {
            throw new PHPUnit_Framework_Error(
                $e->getMessage(),
                0,
                $e->getFile(),
                $e->getLine()
            );
        }
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

    public function testSetRealApp()
    {
        unplug('another');
        option('set', _REAL_APP, null);
        $another = \PMVC\plug(
            'another', [
                _CLASS   => '\PMVC\AnotherPlugin',
                'assert' => _REAL_APP,
            ]
        );
        $mvc = plug('controller');
        $mvc->setApp('testFoo');
        $mvc->plugApp([], ['testFoo' => 'testApp']);
        $this->assertEquals(
            'testApp',
            $another['actual']
        );
    }
}
