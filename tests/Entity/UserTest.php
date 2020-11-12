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
        
        // test that getter always returns ROLE_USER at least
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        
        // test that getter still returns ROLE_USER when initialized with empty array
        $user->setRoles([]);
        $this->assertEquals(['ROLE_USER'], $user->getRoles());

        $user->setRoles(['ROLE_ADMIN']);
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());

        $this->assertEquals("usertest", $user->getUsername());        
        $this->assertEquals("user@test.com", $user->getEmail());
        $this->assertEquals("testpassword", $user->getPassword());
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