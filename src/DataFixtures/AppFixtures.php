<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // create 1 user account
        $user = new User();
        $user->setUsername('utilisateur1');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user, '@dmIn123'
        ));
        $user->setEmail('utilisateur1@test.com');
        $manager->persist($user);

        // create 20 tasks for this user
        for ($i = 1; $i <= 20; ++$i) {
            $task = new Task();
            $task->setTitle('titre de la tâche n° '.$i);
            $task->setContent('contenu de la tâche n° '.$i);
            $task->setUser($user);
            $manager->persist($task);
        }

        // create 1 admin account
        $user = new User();
        $user->setUsername('utilisateur2');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user, '@dmIn123'
        ));
        $user->setEmail('utilisateur2@test.com');
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        // create 20 tasks for this user
        for ($i = 1; $i <= 20; ++$i) {
            $task = new Task();
            $task->setTitle('titre de la tâche n° '.$i);
            $task->setContent('contenu de la tâche n° '.$i);
            $task->setUser($user);
            $manager->persist($task);
        }

        $manager->flush();
    }
}
