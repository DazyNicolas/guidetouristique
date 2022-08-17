<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PublicationRepository;
use App\Entity\Publication;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

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

    public function create(Request $request, EntityManagerInterface $em): Response
    {
        //j'utilise forme builder pour crée le formulaire

        // dd($this->createFormBuilder());

        $publication = new Publication; //permet de récupére le  requete tapper par l'utilsateur

        $form =   $this->createFormBuilder($publication)
            ->add('titre', TextType::class)
            ->add('description', TextareaType::class)
            //  ->add('submit', SubmitType::class, ['label' => 'Crée un publication']) // n'est pas récomander par symfony
            ->getForm();

        // dd($form);

        //Le Request gére la soumission de formulaire

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form->getData()); permet récupére la requet sur le formulaire
            $publication = $form->getData();
            // $publication = new Publication;
            //  $publication->setTitre($data['titre']);
            //  $publication->setDescription($data['description']);

            $em->persist($publication);
            $em->flush();

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

    public function edit(Request $request, Publication $publication, EntityManagerInterface $em): Response
    {
        $form =   $this->createFormBuilder($publication)
        ->add('titre', TextType::class)
        ->add('description', TextareaType::class)
        //  ->add('submit', SubmitType::class, ['label' => 'Crée un publication']) // n'est pas récomander par symfony
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_publications');
        }
        return $this->render('publications/edit.html.twig', [
            'publication' => $publication,
            'form' => $form->createView()
        ]);
    }
}
