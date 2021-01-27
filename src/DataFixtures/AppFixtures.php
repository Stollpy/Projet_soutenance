<?php

namespace App\DataFixtures;

use App\Entity\Shop;
use App\Factory\CategoryFactory;
use App\Factory\ProductFactory;
use App\Factory\ShopFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        UserFactory::new()->createMany(10);
        ShopFactory::new()->createMany(10);
        CategoryFactory::new()->createMany(8);

        // Modifier le nombre a l'interieur pour generer autant que souhaite
        ProductFactory::new()->createMany(100);
        $manager->flush();
    }
}
