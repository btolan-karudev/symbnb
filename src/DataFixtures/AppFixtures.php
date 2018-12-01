<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 30; $i++) {
            $ad = new Ad();

            $ad->setTitle("Titre de l'annonce n°$i")
                ->setSlug("titre-de-l-annonce-n-$i")
                ->setCoverImage("http://placehold.it/1000x300")
                ->setIntroduction("Bonjour a tous cest l intro")
                ->setContent("<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Est, totam.</p>")
                ->setPrice(mt_rand(99, 999))
                ->setRooms(mt_rand(1, 5));

            $manager->persist($ad);

        }

        $manager->flush();
    }
}
