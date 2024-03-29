<?php

namespace PMVC;

class ActionControllerPlugAppTest extends TestCase
{
    protected $resources;

    public function pmvc_setup()
    {
        folders(_RUN_APP, [], [], true);
        $this->resources = __DIR__.'/../resources/';
        unPlug(_RUN_APP);
        unPlug('controller');
        option('set', _REAL_APP, null);
        option('set', _RUN_APPS, null);
    }

    public function testStore()
    {
        $folders = [$this->resources.'apps1', $this->resources.'apps2'];
        $mvc = plug('controller');
        $mvc->setApp('testApp');
        $mvc->plugApp($folders);
        $store = folders(_RUN_APP);
        $expected = array_reverse(
            array_map(function ($d) {
                return realpath($d);
            }, $folders)
        );
        $auto = realpath(__DIR__.'/../../../../pmvc-app');
        if ($auto) {
            $expected[] = $auto;
        }
        $this->assertEquals($expected, $store['folders']);
    }

    public function testPlugApp()
    {
        $folders = [$this->resources.'apps1', $this->resources.'apps2'];
        $mvc = plug('controller');
        $mvc->setApp('testApp');
        $result = $mvc->plugApp($folders);
        $this->assertTrue($result);
        $this->assertEquals('app2', getOption('test'));
    }

    public function testGetAppsParent()
    {
        $mvc = plug('controller');
        $mvc->setApp('testApp');
        $mvc->plugApp([$this->resources.'apps1']);
        $this->assertEquals(
            realpath($this->resources).'/',
            $mvc->getAppsParent()
        );
    }

    public function testGetAppsParentFromVendor()
    {
        $mvc = plug('controller');
        $mvc[_RUN_APPS] = realpath('vendor/pmvc/pmvc');
        $this->assertTrue(0 === strpos($mvc[_RUN_APPS], $mvc->getAppsParent()));
    }

    /**
     * @expectedException Exception
     */
    public function testSetAppFail()
    {
        $this->willThrow(function () {
            $mvc = plug('controller');
            $mvc->setApp('xxx');
            $mvc->plugApp();
        });
    }

    /**
     * @expectedException DomainException
     */
    public function testSetDefaultAppFail()
    {
        $this->willThrow(function () {
            $mvc = plug('controller');
            $mvc->setApp('xxx');
            $mvc[_DEFAULT_APP] = '';
            @$mvc->plugApp();
        }, false);
    }

    public function testSetRealApp()
    {
        unplug('another');
        $another = \PMVC\plug('another', [
            _CLASS   => '\PMVC\AnotherPlugin',
            'assert' => _REAL_APP,
        ]);
        $folders = [$this->resources.'apps1', $this->resources.'apps2'];
        $mvc = plug('controller');
        $mvc->setApp('testFoo');
        $mvc->plugApp($folders, ['testFoo' => 'testapp']);
        $this->assertEquals('testapp', $another['actual']);
    }
}
