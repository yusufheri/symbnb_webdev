<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Role;
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

        $adminRole = new Role();
        $adminRole->setTitle('ADMIN_ROLE');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser
                ->setLastName("Heri")
                ->setFirstName("Yusuf")
                ->setEmail("yusufheri64@gmail.com")
                ->setPicture("https://previews.123rf.com/images/sommersby/sommersby1908/sommersby190800026/129073448-thin-line-hacker-or-coder-icon-on-a-grey-background.jpg")
                ->setHash($this->passwordEncoder->encodePassword($adminUser, '12345678'))
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>'. join('</p><p>', $faker->paragraphs(3)).'</p>')
                ->addUserRole($adminRole)
                ;
        $manager->persist($adminUser);
        
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

        for ($i= 1; $i <= 18; $i++) {
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

            // Gestion des images associées à l'annonces
            for ($j = 1; $j <= mt_rand(2, 3); $j++) {
                $image = new Image();

                $image  ->setUrl($faker->imageUrl())
                        ->setCaption($faker->sentence())
                        ->setAd($ad);

                $manager->persist($image);

            }

            //Gestion des reservations
            for($j= 0; $j <= mt_rand(0, 10); $j++){
                $booking = new Booking();

                $createdAt = $faker->dateTime('-6 months');
                $startDate = $faker->dateTime('-3 months');
                
                $duration = mt_rand(3, 10);

                $endDate = (clone $startDate)->modify("+ ".$duration." days");
                $amount = $ad->getPrice() * $duration;
                $booker = $users[mt_rand(0, count($users) - 1)];

                $comment = $faker->paragraph();

                $booking ->setCreatedAt($createdAt)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setAmount($amount)
                        ->setComment($comment)
                        ->setBooker($booker)
                        ->setAd($ad);

                $manager->persist($booking);

                //Gestion des commentaires
                if(mt_rand(0,1)){
                    $comment = new Comment();
                    $comment->setAd($ad)
                            ->setAuthor($booker)
                            ->setContent($faker->paragraph())
                            ->setRating(mt_rand(1,5));

                    $manager->persist($comment);
                }
                
                
            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
