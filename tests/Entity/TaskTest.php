<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskTest extends WebTestCase
{
    public function testTaskCreate()
    {
        $task = new Task();
        $task->setTitle('tasktitletest');
        $task->setContent('taskcontenttest');

        $this->assertEquals("tasktitletest", $task->getTitle());
        $this->assertEquals("taskcontenttest", $task->getContent());
        $this->assertEquals(false, $task->isDone());    
        $this->assertInstanceOf('DateTime' , $task->getCreatedAt());
    }
}