<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use DateTime;
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

        $task->setCreatedAt(new DateTime('2020-01-01'));
        $this->assertEquals("2020-01-01", $task->getCreatedAt()->format('Y-m-d'));

    }
}