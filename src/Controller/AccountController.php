<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet de se connecter au serveur
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $hasErrors = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasErrors' => $hasErrors,
            'username' => $username
        ]);
    }

    /**
     * Permet de se déconnecter
     * @Route("/logout", name="account_logout")
     * @return void
     */
    public function logout(){

    }

    /**
     * Permet d'afficher le foormulaire d'inscription
     * 
     * @Route("/register", name="account_register")
     *
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Votre compte a été créé avec succès, maintenant vous pouvez vous connecter');
            return $this->redirectToRoute('account_login');

        }

        return $this->render('/account/register.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifer le profil de l'utilisateur
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $manager){
        $user = $this->getUser();
       if (! is_null($user)) {
            $form = $this->createForm(AccountType::class, $user);

            $form = $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', 'Les modifications ont été enregistrées avec succès');
                return $this->redirectToRoute('homepage');
            }
            return $this->render('/account/profile.html.twig', [
                'form' => $form->createView()
            ]); 
       } else {
        $this->addFlash('danger', 'Prière de vous connecter avant de pouvoir modifier votre profil utilisateur !!');
        return $this->redirectToRoute('account_login');
       }
    }

    /**
     * Permet de modifier le mot de passe
     * 
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function updatePassword(Request $request, EntityManagerInterface $manager, 
    UserPasswordEncoderInterface $encoder) {

        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!(password_verify($passwordUpdate->getOldPassword(), $user->getPassword()))) {
                //Gérer le password
                dump('Malheureusement');
            } else {

                $newpassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newpassword);

                $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'votre nouveau mot de passe a été enregistré avec succès'
                );
                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render('/account/password.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisateur en cours
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function myAccount(){
        return $this->render('user/index.html.twig',[
            'user' => $this->getUser()
        ]);
    }
}
