<?php

namespace PMVC;

class ActionForwardTest extends TestCase
{
    protected function pmvc_setup()
    {
        unplug(_RUN_APP);
        unplug('view');
        unplug('fakerouter');
        option('set', _ROUTER, false);
        plug(
            'view',
            [
                _CLASS => '\PMVC\FakeView',
            ]
        );
    }

    private function _fakeForward($type = 'action', $header = '', $path = '')
    {
        return [
            _PATH   => $path,
            _HEADER => $header,
            _TYPE   => $type,
            _ACTION => '',
        ];
    }

    private function _setupRouter()
    {
        option('set', _ROUTER, 'fakerouter');
        plug('fakerouter', [_CLASS => '\PMVC\FakeRouter']);
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
            _CLASS => '\PMVC\FakePlugIn',
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
            $run[_FORWARD][$utKey]
        );
    }

    public function testCleanHeader()
    {
        $fwd = new ActionForward($this->_fakeForward());
        $result = $fwd->cleanHeader();
        $this->assertTrue(is_array($result));
    }

    public function testSetHeaderWithValue()
    {
        $fwd = new ActionForward($this->_fakeForward('action', ['X-Test: value']));
        $this->assertNotEmpty($fwd->getHeader());
    }

    public function testSetClientRedirect()
    {
        $fwd = new ActionForward($this->_fakeForward());
        $this->assertEquals('href', $fwd->setClientRedirect('href'));
        $this->assertEquals('replace', $fwd->setClientRedirect('replace'));
        $this->assertFalse($fwd->setClientRedirect('other'));
    }

    public function testAppendNonView()
    {
        $fwd = new ActionForward($this->_fakeForward());
        $result = $fwd->append(['key' => 'val']);
        $this->assertTrue(is_array($result));
    }

    public function testGoRedirectWithHeaders()
    {
        $this->_setupRouter();
        $fwd = new ActionForward($this->_fakeForward('redirect', ['X-Test: value'], '/home'));
        $fwd->go();
        $this->assertNotNull($fwd);
    }

    public function testGoRedirectWithClientRedirect()
    {
        $this->_setupRouter();
        $fwd = new ActionForward($this->_fakeForward('redirect', '', '/home'));
        $fwd->setClientRedirect('href');
        $fwd->go();
        $this->assertEquals('href', $fwd['clientRedirectType']);
    }

    public function testBuildCommand()
    {
        $this->_setupRouter();
        $fwd = new ActionForward($this->_fakeForward('action', '', '/test'));
        $result = $fwd->buildCommand('/test', []);
        $this->assertEquals('/test', $result);
    }

    public function testGetPathWithMerge()
    {
        $this->_setupRouter();
        $fwd = new ActionForward($this->_fakeForward('action', '', '/test'));
        $result = $fwd->getPath(true);
        $this->assertEquals('/test', $result);
    }

    public function testProcessViewNullPath()
    {
        $this->_setupRouter();
        $fwd = new ActionForward($this->_fakeForward('view', '', null));
        $fwd->name = 'myview';
        $fwd->go();
        $this->assertNotNull($fwd);
    }

    public function testProcessViewWithRunApp()
    {
        $this->_setupRouter();
        $run = plug(_RUN_APP, [_CLASS => '\PMVC\FakePlugIn']);
        $keepForward = new HashMap();
        $keepForward[] = ['foo' => 'bar'];
        $run[_FORWARD] = $keepForward;

        $fwd = new ActionForward($this->_fakeForward('view'));
        $fwd->go();
        $this->assertNotNull($fwd);
    }

    public function testProcessViewWithViewHeaders()
    {
        $this->_setupRouter();
        $view = plug('view');
        $view['headers'] = ['Content-Type: text/html'];

        $fwd = new ActionForward($this->_fakeForward('view'));
        $fwd->go();
        $this->assertNotNull($fwd);
    }

    public function testAppendView()
    {
        $mock = $this->getPMVCMockBuilder('\PMVC\FakeView')
            ->pmvc_onlyMethods(['append'])
            ->getMock();
        $mock->expects($this->exactly(1))
            ->method('append');
        replug('view', [], $mock);
        $fakeForward = [
            _PATH   => '',
            _HEADER => '',
            _TYPE   => 'view',
            _ACTION => '',
        ];
        $forward = new ActionForward($fakeForward);
        $forward->append(['foo' => 'bar']);
    }
}
