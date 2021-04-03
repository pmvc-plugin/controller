<?php

namespace PMVC;

class DefaultFormTest extends TestCase
{
    public function pmvc_setup()
    {
        \PMVC\unplug('controller');
        \PMVC\option('set', [
            _RUN_FORM         => null,
            _DEFAULT_FORM     => null,
            'fakeDefaultForm' => null,
        ]);
    }

    public function testDefaultForm()
    {
        $test_str = 'Hello World!';
        $b = new \PMVC\MappingBuilder();
        $b->addAction(
            'index',
            [
                _FUNCTION => function () use ($test_str) {
                    return $test_str;
                },
            ]
        );
        $option = [
            _DEFAULT_FORM => '\PMVC\FakeDefaultForm',
        ];
        $mvc = \PMVC\plug('controller');
        \PMVC\set($mvc, $option);
        $result = $mvc->process($b);
        $this->assertEquals($test_str, $result[0]);
        $this->assertEquals('aaa', $mvc['fakeDefaultForm']);
    }

    /**
     * @expectedException \DomainException
     */
    public function testFormNotExists()
    {
        $this->willThrow(function () {
            $b = new \PMVC\MappingBuilder();
            $b->addAction(
                'index',
                [
                    _FUNCTION => function () {
                        return '';
                    },
                    _FORM => 'xxx',
                ]
            );
            $mvc = \PMVC\plug('controller');
            $mvc->process($b);
        }, false);
    }

    public function testNotSetDefaultForm()
    {
        $b = new \PMVC\MappingBuilder();
        $b->addAction(
            'index',
            [
                _FUNCTION => function () {
                    return '';
                },
            ]
        );
        $mvc = \PMVC\plug('controller');
        $mvc->process($b);
        $this->assertEquals(null, $mvc['fakeDefaultForm']);
        $this->assertTrue(is_a($mvc[_RUN_FORM], '\PMVC\ActionForm'));
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
