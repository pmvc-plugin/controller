<?php

namespace PMVC;

class ProcessWorkerTest extends TestCase
{
    protected function pmvc_setup()
    {
        if (!class_exists('\PMVC\process_worker')) {
            l(__DIR__.'/../../src/_process_worker');
        }
        replug('annotation', [], new FakeAnnotation());
        replug('amqp', [], new FakeAmqp());
        unplug('supervisor');
        unplug('controller');
    }

    private function _setupAnnotation($attrs = [])
    {
        $fake = new FakeAnnotation();
        $fake->attrs = ['obj' => $attrs];
        replug('annotation', [], $fake);
        return $fake;
    }

    private function _setupSupervisor()
    {
        $fake = new FakeSupervisorPlugin();
        replug('supervisor', [], $fake);
        return $fake;
    }

    private function _makeMappings()
    {
        $b = new MappingBuilder();
        $b->addAction('myworker', function ($m, $f) {
            return [];
        });
        $mappings = new ActionMappings();
        $mappings->set($b);
        return $mappings;
    }

    private function _mockCaller($mappings)
    {
        $caller = $this->getPMVCMockBuilder('\PMVC\controller')
            ->pmvc_onlyMethods(['getMappings', 'processForm', 'getActionFunc'])
            ->getMock();
        $caller->method('getMappings')->willReturn($mappings);
        $caller->method('processForm')->willReturn(new ActionForm());
        $caller->method('getActionFunc')->willReturn(function () {
            return [];
        });
        return $caller;
    }

    public function testInvokeWithNoTaskAttr()
    {
        $caller = $this->_mockCaller($this->_makeMappings());
        $this->_setupAnnotation();
        $supervisor = $this->_setupSupervisor();

        $worker = new process_worker();
        $worker->caller = $caller;
        $worker();

        $this->assertEquals(1, $supervisor->processCallCount);
    }

    public function testInvokeWithDaemonTask()
    {
        $taskAttr = new \stdClass();
        $taskAttr->type = 'daemon';
        $taskAttr->group = null;
        $taskAttr->interval = 5;

        $caller = $this->_mockCaller($this->_makeMappings());
        $this->_setupAnnotation([TASK_KEY => $taskAttr]);
        $supervisor = $this->_setupSupervisor();

        $worker = new process_worker();
        $worker->caller = $caller;
        $worker();

        $this->assertEquals(1, $supervisor->processCallCount);
    }

    public function testInvokeWithScriptTask()
    {
        $taskAttr = new \stdClass();
        $taskAttr->type = 'script';

        $caller = $this->_mockCaller($this->_makeMappings());
        $this->_setupAnnotation([TASK_KEY => $taskAttr]);
        $supervisor = $this->_setupSupervisor();

        $worker = new process_worker();
        $worker->caller = $caller;
        $worker();

        $this->assertEquals(1, $supervisor->processCallCount);
    }

    public function testInvokeWithWrongTaskType()
    {
        $taskAttr = new \stdClass();
        $taskAttr->type = 'unknown';

        $caller = $this->_mockCaller($this->_makeMappings());
        $this->_setupAnnotation([TASK_KEY => $taskAttr]);
        $supervisor = $this->_setupSupervisor();

        $worker = new process_worker();
        $worker->caller = $caller;
        @$worker();

        $this->assertEquals(1, $supervisor->processCallCount);
    }
}
