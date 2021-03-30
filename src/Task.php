<?php

namespace PMVC;

/**
 * Task Attribute.
 */

#[Attribute]
class Task
{
    public $type;
    public $interval;

    public function __construct($type, $payload = [])
    {
        $this->type = $type;
        $this->interval = \PMVC\get($payload, 'interval', 10);
    }
}
