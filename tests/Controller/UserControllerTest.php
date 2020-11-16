<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testUserListActionRequireAuthentication()
    {
        $client = static::createClient();

        $client->request('GET', '/users');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Redirecting to', $client->getResponse()->getContent());
    }

    public function testUserListActionNonAdminIsDenied()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'utilisateur1',
            'PHP_AUTH_PW'   => '@dmIn123',
        ]);

        $client->request('GET', '/users');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testUserListActionAdminIsAllowed()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'utilisateur2',
            'PHP_AUTH_PW'   => '@dmIn123',
        ]);

        $client->request('GET', '/users');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUserCreateAction()
    {
        $client = static::createClient();

        $client->request('GET', '/users/create');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Créer un utilisateur', $client->getResponse()->getContent());
    }

    public function testUserEditActionRequireAuthentication()
    {
        $client = static::createClient();

        // load an existing user, and try to edit its profile      
        $userRepository = static::$container->get(UserRepository::class);
        $userId = $userRepository->findOneBy(['username' => 'utilisateur1'])->getId();
        $client->request('GET', '/users/' . $userId . '/edit');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Redirecting to', $client->getResponse()->getContent());
    }

    public function testUserEditActionNonAdminIsDenied()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'utilisateur1',
            'PHP_AUTH_PW'   => '@dmIn123',
        ]);

        // load an existing user, and try to edit its profile      
        $userRepository = static::$container->get(UserRepository::class);
        $userId = $userRepository->findOneBy(['username' => 'utilisateur1'])->getId();
        $client->request('GET', '/users/' . $userId . '/edit');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testUserEditActionAdminIsAllowed()
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
        
        // submit the edit form with modified data, and control the output
        $client->submitForm('Modifier', [
            'user[username]' => 'jimmy2',
            'user[password][first]' => '@dmIn123',
            'user[password][second]' => '@dmIn123',
            'user[email]' => 'jimmy2@test.com',
            'user[roles]' => 'ROLE_ADMIN',
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertStringContainsString('Superbe', $client->getResponse()->getContent());
        $this->assertStringContainsString('jimmy2', $client->getResponse()->getContent());
    }

    public function testUserEditActionDataTransformer()
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
        
        // submit the edit form with modified data, and control the output
        $client->submitForm('Modifier', [
            'user[username]' => 'jimmy2',
            'user[password][first]' => '@dmIn123',
            'user[password][second]' => '@dmIn123',
            'user[email]' => 'jimmy2@test.com',
            'user[roles]' => 'ROLE_ADMIN',
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertStringContainsString('Superbe', $client->getResponse()->getContent());
        $this->assertStringContainsString('jimmy2', $client->getResponse()->getContent());
    }

    public function testUserCreateActionAndLogin()
    {
        $client = static::createClient();

        // create a new user with the creation form
        $client->request('GET', '/users/create');
        $client->submitForm('Ajouter', [
            'user[username]' => 'testUserCreate',
            'user[password][first]' => '@dmIn123',
            'user[password][second]' => '@dmIn123',
            'user[email]' => 'testUserCreateActionAndLogin@test.com',
            'user[roles]' => 'ROLE_USER',
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertStringContainsString('utilisateur a bien été ajouté', $client->getResponse()->getContent());

        // check that you are redirected to login page
        $this->assertStringContainsString('Nom d\'utilisateur', $client->getResponse()->getContent());
        
        // try to login with the new user
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'testUserCreate',
            '_password' => '@dmIn123',
        ]);        

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertStringContainsString('Bienvenue sur Todo List', $client->getResponse()->getContent());
    }
}
