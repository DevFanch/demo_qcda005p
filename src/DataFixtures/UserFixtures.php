<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher) {}
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        // création d'un administrateur
        $userAdmin = new User();
        $userAdmin->setEmail('admin@admin.com');
        $userAdmin->setLastname('admin');
        $userAdmin->setFirstname('admin');
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, 'admin'));
        $manager->persist($userAdmin);

        // création d'un plannificateur
        $userPlanner = new User();
        $userPlanner->setEmail('planner@demo.com');
        $userPlanner->setLastname('planner');
        $userPlanner->setFirstname('planner');
        $userPlanner->setRoles(['ROLE_PLANNER']);
        $password = $this->userPasswordHasher->hashPassword(
            $userPlanner,
            'planner'
        );
        $userPlanner->setPassword($password);
        $manager->persist($userPlanner);

        // création de 10 utilisateurs classiques
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("user$i@demo.com");
            $user->setLastname($faker->lastName());
            $user->setFirstname($faker->firstName());
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
            $manager->persist($user);
        }
        $manager->flush();
    }
}
