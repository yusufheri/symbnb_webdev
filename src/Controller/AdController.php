<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();
        return $this->render('ad/index.html.twig', [
           'ads' => $ads
        ]);
    }

    /**
     * Permet de créer une nouvelle annonce
     * @Route("/ads/new", name="ads_create")
     * 
     * @return Response
     */

    public function new(Request $request, EntityManagerInterface $manager) {
        
        $ad = new Ad();

        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //  dump($form);
            foreach($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }
            
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash("success", "L'annonce <strong>".$ad->getTitle()."</strong> a bien été enregistrée");

           /*  return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]); */

        }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Affichage d'une annonce
     * @Route("/ads/{slug}", name="ads_show")
     *
     * @return Response
     */
    public function show(Ad $ad) {
        //  $ad = $repo->  findOneBySlug($slug);
        return $this->render('ad/show.html.twig', [
                'ad' => $ad
            ]
        );
    }

    
}
