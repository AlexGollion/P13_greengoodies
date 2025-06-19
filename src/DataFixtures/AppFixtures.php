<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Order;
use App\Entity\OrderProduct;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        $photo = $this->getImagesFiles();

        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->hasher->hashPassword($user, '000000'));
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);

            $manager->persist($user);
        }

        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setName($faker->word . ' ' . $faker->word);
            $product->setShortDescription($faker->sentence);
            $product->setLongDescription($faker->paragraph);
            $product->setPhoto($faker->randomElement($photo));
            $product->setPrice($faker->randomFloat(2, 0, 100));

            $manager->persist($product);
        }

        $manager->flush();
    }

    private function getImagesFiles(): array
    {
        $imgDir = __DIR__ . '/../../assets/images/Product';
        $filesTemp = scandir($imgDir);

        $files = array_filter($filesTemp, function ($file) {
            return $file !== '.' && $file !== '..';
        });

        return $files;
    }
}
