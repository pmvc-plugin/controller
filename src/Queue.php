<?php

namespace PMVC;

/**
 * Task Attribute
 */

#[Attribute]
class Queue 
{
    public $name;
    public $consumer;
    public $publisher;
    public function __construct($name, $payload = [])
    {
      $this->name = $name;
      $this->consumer = in_array('consumer', $payload);
      $this->publisher = in_array('publisher', $payload);
    }
}
