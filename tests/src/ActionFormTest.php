<?php

namespace PMVC;

class ActionFormTest extends TestCase
{
    public function testInvoke()
    {
        $form = new ActionForm();
        $result = $form();
        $this->assertSame($form, $result);
    }
}
