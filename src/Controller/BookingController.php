<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/book", name="booking_create")
     * @Security("is_granted('ROLE_USER')")
     */
    public function book(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        $booking = new Booking();

        $form = $this->createForm(BookingType::class, $booking);

        $form = $form->handleRequest($request);

        if ($form->isSubmitted()  && $form->isValid()){
            $booking->setBooker($this->getUser())
                    ->setAd($ad);

            // Si les dates ne sont pas disponibles, message d'erreur
            if(! $booking->isBookableDates()){
                $this->addFlash(
                    'warning',
                    '<h1>Désolé !!</h1>
                    <b>Les dates que vous avez choisi ne peuvent pas être réservées: elles sont déjà prises
                     par une autre personne</b>'
                );
            } else {
                $manager->persist($booking);
                $manager->flush();

                return $this->redirectToRoute("booking_show", ['id' => $booking->getId(), 'success' => true]);
            }            
        }


        return $this->render('booking/book.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad           
        ]);
    }

    /**
     * Permet d'afficher les détails de l'annonce)
     * @Route("/booking/{id}", name="booking_show")
     * 
     * @param Booking $booking
     * @return Response
     */
    public function show(Booking $booking, Request $request, EntityManagerInterface $manager){
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser());

            
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                '<b>Votre commentaire a été bien pris en compte !</b>'
            );

        }

        return $this->render("booking/show.html.twig", [
            'booking'   => $booking,
            'form'      => $form->createView()
        ]);
    }
}
