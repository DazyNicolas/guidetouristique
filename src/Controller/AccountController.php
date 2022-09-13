<?php

namespace App\Controller;

use App\Form\GuideFormType;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function show(): Response
    {
        return $this->render('account/show.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    #[Route('/account/edit', name: 'app_account_edit')]

    public function edit(Request $request, EntityManagerInterface $em, FlashyNotifier $flashy) : Response
    {
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
}
