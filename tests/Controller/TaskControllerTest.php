<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testListNotDoneAction()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Créer une tâche', $client->getResponse()->getContent());
    }

    public function testListDoneAction()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/done');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Créer une tâche', $client->getResponse()->getContent());
    }

    public function testCreateAction()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/create');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Titre', $client->getResponse()->getContent());
        $this->assertStringContainsString('Contenu', $client->getResponse()->getContent());
    }
}
