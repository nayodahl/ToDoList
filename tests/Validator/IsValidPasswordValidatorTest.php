<?php

namespace App\Tests\Validator;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IsValidPasswordValidatorTest extends WebTestCase
{
    /**
     * @dataProvider passwordProvider
     */
    public function testCreateUserActionNotValidPassword(string $password)
    {
        $client = static::createClient();

        // create a new user with the creation form
        $client->request('GET', '/users/create');
        $client->submitForm('Ajouter', [
            'user[username]' => 'jimmytestCreateUserActionNotValidPassword',
            'user[password][first]' => $password,
            'user[password][second]' => $password,
            'user[email]' => 'jimmytestCreateUserActionNotValidPassword@test.com',
            'user[roles]' => 'ROLE_USER',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('minimum 8 caract', $client->getResponse()->getContent());
    }
    
    public function passwordProvider()
    {
        return [
            ["@dmIn12"], // too short
            ["plop123plop*"], // no uppercase
            ["PLOP123PLOP*"], // no lowercase
            ["plop123PLOP"], // no special char
            ["plopHAHA*plop"], // no number
        ];
    }
}