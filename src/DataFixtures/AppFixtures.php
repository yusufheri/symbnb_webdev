<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');  
        
        $users = [];
        $genres = ['male', 'female'];
       
        for ($j=0; $j < 10; $j++) {

            $user = new User();
            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(0, 99).'.jpg';

            $picture .= ($genre == 'male'? 'men/' : 'women/'). $pictureId;

            $hash = $this->passwordEncoder->encodePassword($user, 'password');

            $user   ->setLastName($faker->lastName)
                    ->setFirstName($faker->firstName($genre))
                    ->setEmail($faker->email)
                    ->setPicture($picture)
                    ->setHash($hash)
                    ->setIntroduction($faker->sentence())
                    ->setDescription('<p>'. join('</p><p>', $faker->paragraphs(3)).'</p>');
            
            $manager->persist($user);

            $users[] = $user;
        }
       
                
        //  dump($users);

        for ($i= 1; $i <= 6; $i++) {
            $ad = new Ad();

            $title = $faker->sentence();
            $imageCover = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content ='<p>'. join('</p><p>', $faker->paragraphs(5)).'</p>';
            $city = $faker->city;
            $lat = $faker->latitude;
            $lng = $faker->longitude;
            $user = $users[mt_rand(0, (count($users) - 1))];

            $ad ->setTitle($title)
                ->setCoverImage($imageCover)                
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setRooms(mt_rand(2, 5))
                ->setPrice(mt_rand(100, 300))
                ->setCity($city)
                ->setLat($lat)
                ->setLng($lng)
                ->setAuthor($user);

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
