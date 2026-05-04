<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Wish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->addCategories($manager);
        $this->addWishes($manager);

    }

    private function addCategories(ObjectManager $manager)
    {
        $categories = ['Travel & Adventure', 'Sport', 'Entertainment', 'Human Relations', 'Others'];

        foreach ($categories as $cate){

            $category = new Category();
            $category->setName($cate);

            $manager->persist($category);
        }

        $manager->flush();
    }



    public function addWishes(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $categories = $manager->getRepository(Category::class)->findAll();

        for ($i = 0; $i < 100; $i++) {
            $wish = new Wish();
            $wish
                ->setDateCreated($faker->dateTimeBetween('-6 month'))
                ->setAuthor($faker->name())
                ->setTitle("Devenir " . $faker->jobTitle())
                ->setIsPublished($faker->boolean(70))
                ->setDescription($faker->paragraph(6))
                ->setCategory($faker->randomElement($categories))
            ;

            $manager->persist($wish);
        }
        $manager->flush();
    }



}
