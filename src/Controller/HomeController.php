<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     *
     * @Route("/", name="homepage")
     */
    public function home(AdRepository $repo, UserRepository $repoUser){
        $data   = $repo->getAvgRatings();
        $users  = $repoUser->findBestUsers();

       return $this->render("home.html.twig",[
           'datatable'  => $data,
           'users'      => $users
       ]);
    }
}


?>