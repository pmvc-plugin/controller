<?php

namespace PMVC;

class AppNotFoundTest extends TestCase
{
    protected function pmvc_setup()
    {
        unplug('controller');
        if (!class_exists('\PMVC\app_not_found')) {
            l(__DIR__.'/../../src/_app_not_found');
        }
    }

    public function testFoundPath()
    {
        $anf = new app_not_found();
        $mock = $this->getPMVCMockBuilder('\PMVC\controller')
            ->pmvc_onlyMethods(['getAppFile', 'setApp'])
            ->getMock();
        $mock->method('getAppFile')->willReturn('/path/to/app.php');
        $mock->expects($this->once())->method('setApp');
        $mock[_DEFAULT_APP] = 'default';
        $anf->caller = $mock;

        $result = @$anf([], 'index.php', ['alias' => '']);
        $this->assertEquals('/path/to/app.php', $result);
    }
}
