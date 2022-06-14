<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Todo;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");

        for ($i = 0; $i < 10; $i++) {
            $todo = new Todo();
            $todo
                ->setTitle($faker->sentence(mt_rand(3, 5)))
                ->setState("Todo")
                ->setDescription($faker->paragraph());

            $manager->persist($todo);
        }
        $manager->flush();
    }
}
