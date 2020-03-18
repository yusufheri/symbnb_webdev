<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Image;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');        

        for ($i= 1; $i <= 6; $i++) {
            $ad = new Ad();

            $title = $faker->sentence();
            $imageCover = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content ='<p>'. join('</p><p>', $faker->paragraphs(5)).'</p>';
            $city = $faker->city;
            $address = $faker->address;
            $lat = $faker->latitude;
            $lng = $faker->longitude;

            $ad ->setTitle($title)
                ->setCoverImage($imageCover)                
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setRooms(mt_rand(2, 5))
                ->setPrice(mt_rand(100, 300))
                ->setCity($city)
                ->setLat($lat)
                ->setLng($lng);

            for ($j = 1; $j <= mt_rand(2, 3); $j++) {
                $image = new Image();

                $image  ->setUrl($faker->imageUrl())
                        ->setCaption($faker->sentence())
                        ->setAd($ad);

                $manager->persist($image);

            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
