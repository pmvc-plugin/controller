<?php

use PMVC\Event;

class ControllerTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        \PMVC\option('set',Event\FINISH, false);
    }

    public function testProcess()
    {
        $b = new PMVC\MappingBuilder();
        $b->addAction(
            'index', [
             'FakeClass',
             'index',
            ]
        );
        $mvc = $this->getMock('\PMVC\controller', ['execute'], [[]]);
        $mvc->expects($this->exactly(2))
            ->method('execute')
            ->will(
                $this->onConsecutiveCalls(
                    (object) [
                    'action' => 'index',
                    ],
                    (object) []
                )
            );
        $mvc->process($b);
    }

    /**
     * @group error
     */
    public function testProcessError()
    {
        $b = new PMVC\MappingBuilder();
        $b->addAction(
            'index',
            [
                _FUNCTION => ['FakeClass', 'index'],
                _FORM     => 'FakeFailForm',
            ]
        );
        $b->addForward(
            'error',
            [
                _PATH => 'hello',
                _TYPE => 'view',
            ]
        );
        $options = [
            \PMVC\ERRORS => [
                \PMVC\USER_ERRORS     => 'erros',
                \PMVC\USER_LAST_ERROR => 'last',
            ],
            _RUN_ACTION => 'index',
        ];
        $mvc = \PMVC\plug('controller');
        \PMVC\set($mvc, $options);
        $view = \PMVC\plug(
            'view', [
            _CLASS => '\PMVC\FakeView',
            ]
        );
        $error = $mvc->process($b);
        $this->assertEquals(
            $options[\PMVC\ERRORS][\PMVC\USER_ERRORS],
            $error[0]['v']['errors']
        );
        $this->assertEquals(
            $options[\PMVC\ERRORS][\PMVC\USER_LAST_ERROR],
            $error[0]['v']['lastError']
        );
    }

    /**
     * @group error
     */
    public function testFinishEventShouldRunOnlyOnce()
    {
        $b = new PMVC\MappingBuilder();
        $b->addAction(
            'index',
            [
                _FUNCTION => ['FakeClass', 'index'],
            ]
        );
        $b->addForward(
            'home',
            [
                _PATH => 'hello',
                _TYPE => 'view',
            ]
        );
        $b->addForward(
            'error',
            [
                _PATH => 'hello',
                _TYPE => 'view',
            ]
        );
        $jsonView = $this->getMock(
            'FakeJsonView',
            ['onFinish'],
            [[]]
        );
        $jsonView->expects($this->once())
            ->method('onFinish');
        \PMVC\replug('view', $jsonView);
        \PMVC\plug(
            'another', [
                _CLASS => 'anotherPlugin',
                'view' => $jsonView
            ]
        );
        $mvc = \PMVC\plug('controller');
        $mvc[\PMVC\ERRORS] = [ 
            \PMVC\USER_ERRORS     => 'erros',
            \PMVC\USER_LAST_ERROR => 'last',
        ];
        $result = $mvc->process($b);
    }

}

class FakeClass extends PMVC\Action
{
    public function index($m, $f)
    {
        $go = $m->get('home');

        return $go;
    }
}

class FakeFailForm extends PMVC\ActionForm
{
    public function validate()
    {
        return false;
    }
}

class anotherPlugin extends \PMVC\PlugIn
{
    public function onFinish()
    {
        $this['view']->process();
    }

    function init()
    {
        \PMVC\plug('dispatcher')
            ->attachAfter($this, Event\FINISH);
    }
}

class FakeJsonView extends \PMVC\FakeView
{

    public function onFinish()
    {
    }

    public function process()
    {
        if (\PMVC\getOption(Event\FINISH)) {
            // run directly if miss event
            return $this->onFinish();
        } else {
            // only run by finish event 
            \PMVC\plug('dispatcher')
                ->attachAfter($this, Event\FINISH);
        }
    }
}
