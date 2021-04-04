<?php

namespace PMVC;

class HelloTest extends TestCase
{
    public function pmvc_setup()
    {
        \PMVC\unplug('controller');
    }

    public function testHello()
    {
        $test_str = 'Hello World!';
        $b = new \PMVC\MappingBuilder();
        $b->addAction(
            'index',
            function () use ($test_str) {
                return $test_str;
            }
        );
        $mvc = \PMVC\plug('controller');
        $result = $mvc->process($b);
        $this->assertEquals($test_str, $result[0]);
    }

    /**
     * @dataProvider actionProvider
     */
    public function testActionCaseSensitive($addAction, $processAction)
    {
        $test_str = 'Hello World!';
        $b = new \PMVC\MappingBuilder();
        $b->addAction(
            $addAction,
            function () use ($test_str) {
                return $test_str;
            }
        );
        $mvc = \PMVC\plug('controller');
        $mvc->setAppAction($processAction);
        $result = $mvc->process($b);
        $this->assertEquals($test_str, $result[0]);
    }

    public function actionProvider()
    {
        return [
            ['IndexTest', 'indextest'],
            ['IndexTest', 'IndexTest'],
            ['indextest', 'IndexTest'],
        ];
    }
}
