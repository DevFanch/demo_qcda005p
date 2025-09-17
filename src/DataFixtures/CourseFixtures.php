<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Course;
use App\Entity\Trainer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CourseFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        //Permet d'utiliser Faker
        $faker = \Faker\Factory::create('fr_FR');

        $cours= new Course();
        $cours->setName('Symfony');
        $cours->setContent('Le développement web côté Serveur avec Symfony');
        $cours->setDuration(10);
        $cours->setDateCreated(new \DateTimeImmutable('2025-09-01'));
        $cours->setCategory($this->getReference('category1',Category::class));
        $this->addTrainers($cours);
        $manager->persist($cours);

        $cours= new Course();
        $cours->setName('PHP');
        $cours->setContent('Le développement web côté Serveur avec PHP');
        $cours->setDuration(5);
        $cours->setDateCreated(new \DateTimeImmutable('2025-08-01'));
        $cours->setCategory($this->getReference('category1',Category::class));
        $this->addTrainers($cours);
        $manager->persist($cours);

        $cours= new Course();
        $cours->setName('Apache');
        $cours->setContent('Administration d\'un serveur Apache sous Linux');
        $cours->setDuration(5);
        $cours->setDateCreated(new \DateTimeImmutable('2024-05-01'));
        $cours->setCategory($this->getReference('category2',Category::class));
        $this->addTrainers($cours);
        $manager->persist($cours);

        for($i=1;$i<=30;$i++){
            $cours= new Course();
            $cours->setName($faker->word());
            $cours->setContent($faker->realText());
            $cours->setDuration(mt_rand(1,10));
            $dateCreated=$faker->dateTimeBetween('-2 months', 'now');
            $cours->setDateCreated(\DateTimeImmutable::createFromMutable( $dateCreated));
            $dateModified=$faker->dateTimeBetween($dateCreated->format('Y-m-d'), 'now');
            $cours->setDateModified(\DateTimeImmutable::createFromMutable( $dateModified));
            $cours->setCategory($this->getReference('category'.mt_rand(1,2),Category::class));
            $this->addTrainers($cours);
            $manager->persist($cours);
        }

        $manager->flush();
    }

    private function addTrainers(Course $cours) :void{
        for($i=0;$i<=mt_rand(0,5);$i++){
            $trainer=$this->getReference('trainer'.rand(1,20),Trainer::class);
            $cours->addTrainer($trainer);
        }
    }

    public function getDependencies():array{
        return [CategoryFixtures::class,TrainerFixtures::class];
    }

}
