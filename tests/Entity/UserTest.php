<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    public function testUserCreate()
    {
        $user = new User();
        $user->setUsername('usertest');
        $user->setEmail('user@test.com');
        $user->setPassword('testpassword');
        $user->setRoles([]);

        $this->assertEquals("usertest", $user->getUsername());        
        $this->assertEquals("user@test.com", $user->getEmail());
        $this->assertEquals("testpassword", $user->getPassword());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testUserCreateAndAddTaskAndRemoveTask()
    {
        $user = new User();
        $task = new Task();
        $task->setTitle('tasktitletest');
        $task->setContent('taskcontenttest');
        $user->addTask($task);        

        $tasks=$user->getTasks()->first();
        $this->assertEquals("tasktitletest", $tasks->getTitle());
        $this->assertEquals("taskcontenttest", $tasks->getContent());    
        
        $user->removeTask($task);

        $this->assertEquals(true, $user->getTasks()->isEmpty());
    }
}