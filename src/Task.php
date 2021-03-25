<?php

namespace PMVC;

/**
 * Task Attribute
 */

#[Attribute]
class Task
{
    public $type;
    public function __construct($type)
    {
      $this->type = $type;
    }
}
