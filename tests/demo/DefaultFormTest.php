<?php

class DefaultFormTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        \PMVC\unplug('controller');
    }

    public function testDefaultForm()
    {
        $test_str = 'Hello World!';
        $b = new PMVC\MappingBuilder();
        $b->addAction(
            'index', [
                _FUNCTION => function () use ($test_str) {
                    return $test_str;
                },
                _FORM => 'myForm',
            ]
        );
        $option = [
            _DEFAULT_FORM => 'FakeDefaultForm',
        ];
        $mvc = \PMVC\plug('controller');
        $mvc->setOption($option);
        $result = $mvc->process($b);
        $this->assertEquals($test_str, $result[0]);
        $this->assertEquals('aaa', \PMVC\getOption('fakeDefaultForm'));
    }
}

class FakeDefaultForm extends \PMVC\ActionForm
{
    public function validate()
    {
        \PMVC\option('set', 'fakeDefaultForm', 'aaa');

        return true;
    }
}
