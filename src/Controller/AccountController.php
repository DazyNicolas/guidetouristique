<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\GuideFormType;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function show( FlashyNotifier $flashy): Response
    {
        if(!$this->getUser()){
            $flashy->warning('Vous devez d\'abord vous connecter');
            return $this->redirectToRoute('app_login'); 
        }

        return $this->render('account/show.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    #[Route('/account/edit', name: 'app_account_edit')]

    public function edit(Request $request, EntityManagerInterface $em, FlashyNotifier $flashy) : Response
    {
        if(! $this->getUser()){
            $flashy->warning('Vous devez d\'abord vous connecter');
            return $this->redirectToRoute('app_login'); 
           
        }

        $user = $this->getUser();
        $form =  $this->createForm(GuideFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $em->flush();
            
            $flashy->success('Modification réussie');

            return $this->redirectToRoute('app_account');
        }


        return $this->render('account/edit.html.twig', [
            'form'=> $form->createView()
        ]);
    }

    #[Route('/account/change-password', name: 'app_account_change_password')]
    public function changePassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $PasswordHash, FlashyNotifier $flashy) : Response
    {
        if(! $this->getUser()){
            $flashy->warning('Vous devez d\'abord vous connecter');
            return $this->redirectToRoute('app_login'); 
           
        }

        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class, null, [
            'current_password_is_required' => true
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 

            $user->setPassword(
                $PasswordHash->hashPassword($user, $form['plainPassword']->getData())
            );
            
            //dd($user);
            $em->flush();

            $flashy->success('Mot de passe mis à jour avec succès');

             return $this->redirectToRoute('app_account');
        }
        
        return $this->render('account/change_password.html.twig', [
            'form' => $form->createView()
        ]);
    }


}
