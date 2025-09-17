<?php

namespace App\DataFixtures;

use App\Entity\Trainer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TrainerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i <= 20; $i++) {
            $trainer = new Trainer();
            $trainer->setFirstName($faker->firstName());
            $trainer->setLastName($faker->lastName());
            $trainer->setDateCreated(\DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-2 months', 'now')));
            $manager->persist($trainer);
            $this->addReference('trainer'.$i, $trainer);
        }

        $manager->flush();
    }
}
