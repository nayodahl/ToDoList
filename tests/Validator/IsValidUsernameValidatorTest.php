<?php

namespace App\Tests\Validator;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IsValidUsernameValidatorTest extends WebTestCase
{
    /**
     * @dataProvider usernameProvider
     */    
    public function testCreateUserActionNotValidUsername(string $username)
    {
        $client = static::createClient();

        // create a new user with the creation form
        $client->request('GET', '/users/create');
        $client->submitForm('Ajouter', [
            'create_user[username]' => $username,
            'create_user[password][first]' => '@dmIn123',
            'create_user[password][second]' => '@dmIn123',
            'create_user[email]' => 'jimmy@test.com',
            'create_user[roles]' => 'ROLE_USER',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('entre 4 et 16 caract', $client->getResponse()->getContent());
    }

    public function usernameProvider()
    {
        return [
            ["jjj"], // too short
            ["ploooooooooooooooooooooo"], // too long
            ["username@"], // forbidden special char
        ];
    }
}