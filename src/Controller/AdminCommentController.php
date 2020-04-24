<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comment", name="admin_comment_index")
     */
    public function index(CommentRepository $comment)
    {        
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $comment->findAll(),
        ]);
    }

    /**
     * Permet de modifier le commentaire
     *
     * @Route("/admin/comment/{id}/edit", name="admin_comment_edit")
     * @param Comment $comment
     * @return Response
     */
    public function edit(Comment $comment, Request $request){

        $form = $this->createForm(AdminCommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->addFlash("success", "Le commentaire n° ". $comment->getId()." a été enregistré avec succès");
        }
        return $this->render("admin/comment/edit.html.twig", [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }

    /**
     * Permet de supprimer un commentaire
     * 
     * @Route("admin/comment/{id}/delete", name="admin_comment_delete")
     *
     * @param Comment $comment
     * @return Response
     */
    public function delete(Comment $comment, EntityManagerInterface $manager){

        $message = "Le commentaire de {$comment->getAuthor()->getFullName()} a été supprimé avec succès";
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            "success",
            $message
        );

        return $this->redirectToRoute("admin_comment_index"); 
       
    }
}
