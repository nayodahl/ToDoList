<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testTasksListNotDoneAction()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Créer une tâche', $client->getResponse()->getContent());
    }

    public function testTasksListDoneAction()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/done');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Créer une tâche', $client->getResponse()->getContent());
    }

    public function testTasksCreateAction()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/create');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Titre', $client->getResponse()->getContent());
        $this->assertStringContainsString('Contenu', $client->getResponse()->getContent());
    }

    public function testTasksCreateActionSubmitForm()
    {
        $client = static::createClient();

        // create a new task with the creation form, and test this form
        $client->request('GET', '/tasks/create');
        $client->submitForm('Ajouter', [
            'task[title]' => 'Titre de tache de test',
            'task[content]' => 'Contenu de tache de test',
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertStringContainsString('La tâche a été bien été ajoutée', $client->getResponse()->getContent());
    }

    public function testTasksEditActionDenyIfNotAdminAndAuthorIsAnonymous()
    {
        $client = static::createClient();

        // create a new task with anonymous author, so without being authenticated
        $client->request('GET', '/tasks/create');
        $client->submitForm('Ajouter', [
            'task[title]' => 'Titre de tache de test anonyme',
            'task[content]' => 'Contenu de tache de test',
        ]);
        
        self::ensureKernelShutdown();
        // login as non admin user utilisateur1
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'utilisateur1',
            'PHP_AUTH_PW'   => '@dmIn123',
        ]);

        // load the new task and try to edit it as non admin, you should be denied
        $taskRepository = static::$container->get(TaskRepository::class);
        $taskId = $taskRepository->findOneBy(['title' => 'Titre de tache de test anonyme'])->getId();
        $client->request('GET', '/tasks/' . $taskId . '/edit');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testTasksEditActionAllowIfAdminAndAuthorIsAnonymous()
    {
        $client = static::createClient();

        // create a new task with anonymous author, so without being authenticated
        $client->request('GET', '/tasks/create');
        $client->submitForm('Ajouter', [
            'task[title]' => 'Titre de tache de test anonyme',
            'task[content]' => 'Contenu de tache de test',
        ]);
        
        self::ensureKernelShutdown();
        // login as admin user utilisateur2
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'utilisateur2',
            'PHP_AUTH_PW'   => '@dmIn123',
        ]);

        // load the new task and try to edit it as admin, you should be granted
        $taskRepository = static::$container->get(TaskRepository::class);
        $taskId = $taskRepository->findOneBy(['title' => 'Titre de tache de test anonyme'])->getId();
        $client->request('GET', '/tasks/' . $taskId . '/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Titre de tache de test anonyme', $client->getResponse()->getContent());
    }

    public function testTasksEditActionDenyIfUserIsNotTheAuthor()
    {
        // login as utilisateur1
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'utilisateur1',
            'PHP_AUTH_PW'   => '@dmIn123',
        ]);

        // load a task of utilisateur2 and try to edit it
        $taskRepository = static::$container->get(TaskRepository::class);
        $taskId = $taskRepository->findOneBy(['title' => 'titre de la tâche n° 1 de utilisateur2'])->getId();
        $client->request('GET', '/tasks/' . $taskId . '/edit');


        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertStringContainsString('Oops !', $client->getResponse()->getContent());
    }

    public function testTasksEditActionSubmitForm()
    {
        // login as admin user utilisateur2
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'utilisateur2',
            'PHP_AUTH_PW'   => '@dmIn123',
        ]);

        // load an existing task and access the edit form
        $taskRepository = static::$container->get(TaskRepository::class);
        $taskId = $taskRepository->findOneBy(['title' => 'titre de la tâche n° 2 de utilisateur2'])->getId();
        $client->request('GET', '/tasks/' . $taskId . '/edit');

        // submit form with updated data and control
        $client->submitForm('Modifier', [
            'task[title]' => 'titre de la tâche n° 2 de utilisateur2 modifié',
            'task[content]' => 'titre de la tâche n° 2 de utilisateur2 modifié',
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertStringContainsString('Superbe !', $client->getResponse()->getContent());
    }

    public function testTasksToggleTaskAction()
    {
        $client = static::createClient();

        // load an existing task, by default isDone = false, and then toggle it
        $taskRepository = static::$container->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['title' => 'titre de la tâche n° 1 de utilisateur1']);
        $taskId = $task->getId();
        $client->request('GET', '/tasks/' . $taskId . '/toggle');

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertStringContainsString('Superbe !', $client->getResponse()->getContent());

        // assert that task property isDone has changed
        $this->assertEquals(true, $task->isDone());
    }

    //////////////////////////////////////////////

    public function testTasksDeleteActionDenyIfNotAdminAndAuthorIsAnonymous()
    {
        $client = static::createClient();

        // create a new task with anonymous author, so without being authenticated
        $client->request('GET', '/tasks/create');
        $client->submitForm('Ajouter', [
            'task[title]' => 'Titre testTasksDeleteActionDenyIfNotAdminAndAuthorIsAnonymous',
            'task[content]' => 'Contenu testTasksDeleteActionDenyIfNotAdminAndAuthorIsAnonymous',
        ]);
        
        self::ensureKernelShutdown();
        // login as non admin user utilisateur1
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'utilisateur1',
            'PHP_AUTH_PW'   => '@dmIn123',
        ]);

        // load the new task and try to delete it as non admin, you should be denied
        $taskRepository = static::$container->get(TaskRepository::class);
        $taskId = $taskRepository->findOneBy(['title' => 'Titre testTasksDeleteActionDenyIfNotAdminAndAuthorIsAnonymous'])->getId();
        $client->request('GET', '/tasks/' . $taskId . '/delete');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testTasksDeleteActionAllowIfAdminAndAuthorIsAnonymous()
    {
        $client = static::createClient();

        // create a new task with anonymous author, so without being authenticated
        $client->request('GET', '/tasks/create');
        $client->submitForm('Ajouter', [
            'task[title]' => 'Titre testTasksDeleteActionAllowIfAdminAndAuthorIsAnonymous',
            'task[content]' => 'Contenu testTasksDeleteActionAllowIfAdminAndAuthorIsAnonymous',
        ]);
        
        self::ensureKernelShutdown();
        // login as admin user utilisateur2
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'utilisateur2',
            'PHP_AUTH_PW'   => '@dmIn123',
        ]);

        // load the new task and try to delete it as admin, you should be granted
        $taskRepository = static::$container->get(TaskRepository::class);
        $taskId = $taskRepository->findOneBy(['title' => 'Titre testTasksDeleteActionAllowIfAdminAndAuthorIsAnonymous'])->getId();
        $client->request('GET', '/tasks/' . $taskId . '/delete');

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertStringContainsString('supprim', $client->getResponse()->getContent());
    }

    public function testTasksDeleteActionDenyIfUserIsNotTheAuthor()
    {
        // login as utilisateur1
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'utilisateur1',
            'PHP_AUTH_PW'   => '@dmIn123',
        ]);

        // load a task of utilisateur2 and try to edit it
        $taskRepository = static::$container->get(TaskRepository::class);
        $taskId = $taskRepository->findOneBy(['title' => 'titre de la tâche n° 1 de utilisateur2'])->getId();
        $client->request('GET', '/tasks/' . $taskId . '/delete');


        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertStringContainsString('Oops !', $client->getResponse()->getContent());
    }

    public function testTasksDeleteActionSubmitForm()
    {
        // login as admin user utilisateur2
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'utilisateur2',
            'PHP_AUTH_PW'   => '@dmIn123',
        ]);

        // load an existing task and delete it
        $taskRepository = static::$container->get(TaskRepository::class);
        $taskId = $taskRepository->findOneBy(['title' => 'titre de la tâche n° 2 de utilisateur2'])->getId();
        $client->request('GET', '/tasks/' . $taskId . '/delete');

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertStringContainsString('Superbe !', $client->getResponse()->getContent());
    }
}
