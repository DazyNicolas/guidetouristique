<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PublicationRepository;
use App\Entity\Publication;
use App\Form\PublicationType;
use App\Repository\GuideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;


class PublicationsController extends AbstractController
{
    #[Route('/publications', name: 'app_publications', methods: "GET")]

    public function index(PublicationRepository $publicationsRepository): Response
    {

        // dump($publicationsRepository ->findAll());
        $publications = $publicationsRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('publications/index.html.twig', compact('publications'));
    }


    #[Route('/publications/create', name: 'app_publication_create', methods: "GET|POST")]

    public function create(Request $request, EntityManagerInterface $em, FlashyNotifier $flashy, GuideRepository $guideRepo): Response
    {
       

        if(!$this->getUser()){

            $flashy->warning('Vous devez d\'abord vous connecter');
            return $this->redirectToRoute('app_login'); 
        };

        if ($this->getUser()->getIsVerified()){
            $flashy->warning('Vous devez avoir un compte vérifié');
            return $this->redirectToRoute('app_home'); 
        }

        $publication = new Publication; //permet de récupére le  requete tapper par l'utilsateur

        $form =   $this->createForm(PublicationType::class, $publication);

    
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $publication ->setGuide($this->getUser());
            $em->persist($publication);
            $em->flush();

           // $this->addFlash('success', 'Publication créé avec succès');
            $flashy->success('Publication créé avec succès');

            return $this->redirectToRoute('app_publications');
        }



        return $this->render(
            'publications/create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/publications/{id<[0-9]+>}', name: 'app_publication_show', methods: "GET")]

    public function show(Publication $publication): Response
    {
        return $this->render('publications/show.html.twig', compact('publication'));
    }


    #[Route('/publications/{id<[0-9]+>}/edit', name: 'app_publication_edit', methods: "GET|POST")]

    public function edit(Request $request, Publication $publication, EntityManagerInterface $em, FlashyNotifier $flashy): Response
    {

        if(!$this->getUser()){

            $flashy->warning('Vous devez d\'abord vous connecter');
            return $this->redirectToRoute('app_login'); 
        };

        if (!$this->getUser()->getIsVerified()){
            $flashy->warning('Vous devez avoir un compte vérifié');
            return $this->redirectToRoute('app_home'); 
        }

        if ($publication->getGuide() != $this->getUser()){
            $flashy->warning('Vous n \'êtes pas l\'auteur de ce post');
            return $this->redirectToRoute('app_home'); 
        }


        $form =   $this->createForm(PublicationType::class, $publication);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

          //  $this->addFlash('success', 'mise à jour réussie');
            $flashy->success('mise à jour réussie');

            return $this->redirectToRoute('app_publications');
        }
        return $this->render('publications/edit.html.twig', [
            'publication' => $publication,
            'form' => $form->createView()
        ]);
    }
    
    #[Route('/publications/{id<[0-9]+>}', name: 'app_publication_delete', methods: "POST")]

    public function delete(Request $request, Publication $publication, EntityManagerInterface $em,  FlashyNotifier $flashy)
    {
        if(!$this->getUser()){

            $flashy->warning('Vous devez d\'abord vous connecter');
            return $this->redirectToRoute('app_login'); 
        };

        if (!$this->getUser()->getIsVerified()){
            $flashy->warning('Vous devez avoir un compte vérifié');
            return $this->redirectToRoute('app_home'); 
        }

        if (!$publication->getGuide() != $this->getUser()){
            $flashy->warning('Vous n\'êtes pas l\'auter de ce post');
            return $this->redirectToRoute('app_home'); 
        }

       // dd($request->request->get('csrf_token'));
        if($this->isCsrfTokenValid('publication_deletion_'. $publication->getId(),$request->request->get('csrf_token'))){
            $em->remove($publication);
            $em->flush();


           // $this->addFlash('info', 'supprimé avec succès');
            $flashy->primarydark('supprimé avec succès');
            
        }
            
      
       
       return $this->redirectToRoute('app_publications');
    }
}
