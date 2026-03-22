<?php

namespace PMVC;

class ActionMappingsTest extends TestCase
{
    public function testAddWhenNotEmpty()
    {
        $mappings = new ActionMappings();
        $b1 = new MappingBuilder();
        $b1->addAction('action1');
        $mappings->add($b1);

        $b2 = new MappingBuilder();
        $b2->addAction('action2');
        $mappings->add($b2);

        $this->assertTrue($mappings->actionExists('action1'));
        $this->assertTrue($mappings->actionExists('action2'));
    }

    public function testKeySet()
    {
        $mappings = new ActionMappings();
        $b = new MappingBuilder();
        $b->addAction('myAction');
        $mappings->set($b);

        $keys = $mappings->keySet();
        $this->assertContains('myaction', $keys);
    }

    public function testFindForwardWithEmptyName()
    {
        $mappings = new ActionMappings();
        $b = new MappingBuilder();
        $mappings->set($b);

        $result = @$mappings->findForward('');
        $this->assertFalse($result);
    }

    public function testFindForwardNotFound()
    {
        $mappings = new ActionMappings();
        $b = new MappingBuilder();
        $b->addAction('action');
        $mappings->set($b);

        $result = @$mappings->findForward('nonexistent');
        $this->assertFalse($result);
    }

    public function testFindFormWithCallable()
    {
        $mappings = new ActionMappings();
        $b = new MappingBuilder();
        $b->addForm('myform', [_CLASS => '\PMVC\FakeFormFactory::create']);
        $mappings->set($b);

        $result = $mappings->findForm('myform');
        $this->assertInstanceOf('\PMVC\ActionForm', $result);
    }

    public function testOffsetExistsOnActionMapping()
    {
        $mappings = new ActionMappings();
        $b = new MappingBuilder();
        $b->addAction('action');
        $b->addForward('myforward', [_PATH => '/foo', _TYPE => 'action']);
        $mappings->set($b);

        $a = $mappings->findAction('action');
        $this->assertTrue(isset($a['myforward']));
        $this->assertFalse(isset($a['nonexistent']));
    }
}

class FakeFormFactory
{
    public static function create()
    {
        return new ActionForm();
    }
}
