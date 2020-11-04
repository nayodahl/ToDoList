<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testListActionRequireAuthentication()
    {
        $client = static::createClient();

        $client->request('GET', '/users');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Redirecting to', $client->getResponse()->getContent());
    }

    public function testListActionNonAdminIsDenied()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'utilisateur1',
            'PHP_AUTH_PW'   => '@dmIn123',
        ]);

        $client->request('GET', '/users');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testListActionAdminIsAllowed()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'utilisateur2',
            'PHP_AUTH_PW'   => '@dmIn123',
        ]);

        $client->request('GET', '/users');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateAction()
    {
        $client = static::createClient();

        $client->request('GET', '/users/create');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Créer un utilisateur', $client->getResponse()->getContent());
    }

    public function testEditActionRequireAuthentication()
    {
        $client = static::createClient();

        $client->request('GET', '/users/22/edit');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Redirecting to', $client->getResponse()->getContent());
    }

    public function testEditActionNonAdminIsDenied()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'utilisateur1',
            'PHP_AUTH_PW'   => '@dmIn123',
        ]);

        $client->request('GET', '/users/22/edit');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testEditActionAdminIsAllowed()
    {
        // loggin as admin
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'utilisateur2',
            'PHP_AUTH_PW'   => '@dmIn123',
        ]);

        // create a new user with the creation form, and test this form
        $client->request('GET', '/users/create');
        $client->submitForm('Ajouter', [
            'user[username]' => 'jimmy',
            'user[password][first]' => '@dmIn123',
            'user[password][second]' => '@dmIn123',
            'user[email]' => 'jimmy@test.com',
            'user[roles]' => 'ROLE_USER',
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertStringContainsString('utilisateur a bien été ajouté', $client->getResponse()->getContent());

        // try now to access the edit form, editing this new user
        
        $userRepository = static::$container->get(UserRepository::class);
        $userId = $userRepository->findOneBy(['username' => 'jimmy'])->getId();
        $client->request('GET', '/users/' . $userId . '/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('jimmy', $client->getResponse()->getContent());        
    }
}
