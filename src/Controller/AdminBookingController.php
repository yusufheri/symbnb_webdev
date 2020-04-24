<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Service\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_booking_index")
     */
    public function index($page, Paginator $paginator)
    {
        $paginator  ->setEntityClass(Booking::class)
                    ->setPage($page);

        return $this->render('admin/booking/index.html.twig', [
            'paginator' => $paginator
        ]);
    }

    /**
     * Permet d'éditer la réservation
     *
     * @Route("admin/bookings/{id}/edit", name="admin_booking_edit")
     * @param Booking $booking
     * @param Request $request
     * @return Response
     */
    public function edit(Booking $booking, Request $request, EntityManagerInterface $manager){
        $form = $this->createForm(AdminBookingType::class, $booking);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $booking->setAmount(0);

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                "success",
                "La réservation n°{$booking->getId()} a été modifiée avec succès"
            );

            return $this->redirectToRoute("admin_booking_index");
        }

        return $this->render("admin/booking/edit.html.twig",[
            'form' => $form->createView(),
            'booking' => $booking
        ]);
    }

    /**
     * Permet de supprimer une réservation
     * 
     * @Route("admin/bookings/{id}/delete", name="admin_booking_delete")
     */
    public function delete(Booking $booking, EntityManagerInterface $manager){
        $message = "La réservation n° {$booking->getId()} de l'annonce <b>{$booking->getAd()->getTitle()}</b> a été bel et bien été supprimé avec succès";
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            "success",
            $message
        );

        return $this->redirectToRoute("admin_booking_index");
    }
}
