<?php

namespace App\DataFixtures;

use App\Entity\Wish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for($i = 0; $i < 100; $i++){
            $wish = new Wish();
            $wish
                ->setDateCreated($faker->dateTimeBetween('-6 month'))
                ->setAuthor($faker->name())
                ->setTitle("Devenir " . $faker->jobTitle())
                ->setIsPublished($faker->boolean(70))
                ->setDescription($faker->paragraph(6));

            $manager->persist($wish);

            }

        $manager->flush();
    }
}
