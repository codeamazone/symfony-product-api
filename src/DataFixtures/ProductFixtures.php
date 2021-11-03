<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {       
        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product->setName("product{$i}");
            $product->setGtin(str_shuffle('12345678901234'));
            $product->setDescription("description{$i}");
            $product->setColor("color{$i}");
            $product->setPrice(rand() / 10);            
            $manager->persist($product);

            $manager->flush();

        }
            
    }

    
}