<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class Stats{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getStats(){

        $users = $this->getUsersCount();
        $ads = $this->getAdsCount();
        $bookings = $this->getBookingsCount();
        $comments = $this->getCommentsCount();
        $bestAds = $this->getAVGComments();
        $worstAds =$this->getAVGComments('ASC');

        return compact('users', 'ads', 'bookings', 'comments','bestAds','worstAds');
    }

    public function getAVGComments($orderBy = 'DESC', $limit = 5){
        
       return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture 
            FROM App\Entity\Comment c
            JOIN c.ad a
            JOIN c.author u
            GROUP BY a
            ORDER BY note '.$orderBy
        )
        ->setMaxResults($limit)
        ->getResult();
    }

    public function getUsersCount(){
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getAdsCount(){
        return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
    }

    public function getCommentsCount(){
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
    }

    public function getBookingsCount(){
        return $this->manager->createQuery('SELECT COUNT(b) FROM App\Entity\Booking b')->getSingleScalarResult();
    }
}