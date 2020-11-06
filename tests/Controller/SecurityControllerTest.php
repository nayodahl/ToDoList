<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginActionFormIsRendered()
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Se connecter', $client->getResponse()->getContent());
    }

    public function testLoginActionLoginSuccessfully()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'utilisateur1',
            '_password' => '@dmIn123',
        ]);
        
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertStringContainsString('Se déconnecter', $client->getResponse()->getContent());
        $this->assertStringContainsString('utilisateur1', $client->getResponse()->getContent());
    }

    public function testLoginActionLoginFailedWrongPassword()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'utilisateur1',
            '_password' => 'wrongpassword',
        ]);
        
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertStringContainsString('Identifiants invalides', $client->getResponse()->getContent());
    }

    public function testLoginActionLoginFailedWrongUsername()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'wronguser',
            '_password' => '@dmIn123',
        ]);
        
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertStringContainsString('a pas pu être trouvé', $client->getResponse()->getContent());
    }
}