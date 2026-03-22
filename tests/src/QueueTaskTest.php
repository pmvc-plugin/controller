<?php

namespace PMVC;

class QueueTaskTest extends TestCase
{
    public function testQueueDefault()
    {
        $q = new Queue('test-queue');
        $this->assertEquals('test-queue', $q->name);
        $this->assertFalse($q->consumer);
        $this->assertFalse($q->publisher);
    }

    public function testQueueWithPayload()
    {
        $q = new Queue('test-queue', ['consumer', 'publisher']);
        $this->assertTrue($q->consumer);
        $this->assertTrue($q->publisher);
    }

    public function testTaskDefault()
    {
        $t = new Task('script');
        $this->assertEquals('script', $t->type);
        $this->assertEquals(10, $t->interval);
        $this->assertNull($t->group);
        $this->assertNull($t->trigger);
    }

    public function testTaskWithPayload()
    {
        $t = new Task('daemon', ['interval' => 5, 'group' => 'workers', 'trigger' => 'cron']);
        $this->assertEquals(5, $t->interval);
        $this->assertEquals('workers', $t->group);
        $this->assertEquals('cron', $t->trigger);
    }
}
