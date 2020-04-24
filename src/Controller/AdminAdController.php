<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads", name="admin_ads_index")
     */
    public function index(AdRepository $repo)
    {
        return $this->render('admin/ads/index.html.twig', [
           'ads' => $repo->findAll()
        ]);
    }

    /**
     * Permet de modifier une annonce par l'Admin
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     * @param Ad $ad
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager){

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Modification portées à l annonce <strong>".$ad->getTitle()."</strong> ont été bien enregistrées"
            );
            return $this->redirectToRoute('admin_ads_index');
        }
        return $this->render('admin/ads/edit.html.twig',[
            'ad'   => $ad,
            'form' => $form->createView(),            
        ]);
    }

    /**
     * Permet de supprimer une annonce par l'Administrateur
     *
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     * @Security("is_granted('ROLE_USER')", message="Accès réfusé ! Vous devez vous connecter pour voir vos réservation")
     * 
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Ad $ad, EntityManagerInterface $manager){
        if (count($ad->getBookings()) > 0) {
            $this->addFlash(
                'info',
                "L'annonce que vous voulez supprimer possèder des réservations !! Pas moyen de supprimer l'annonce 
                <strong>{$ad->getTitle()}</strong>"
            );
        } else {
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a été supprimée avec succès"
            );
        }

        return $this->redirectToRoute('admin_ads_index');
    }
}
