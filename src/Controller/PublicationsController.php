<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PublicationRepository;
use App\Entity\Publication;

class PublicationsController extends AbstractController
{
    #[Route('/publications', name: 'app_publications')]

    public function index(PublicationRepository $publicationsRepository): Response
    {

       // dump($publicationsRepository ->findAll());
       $publications = $publicationsRepository -> findAll();
        return $this->render('publications/index.html.twig', compact('publications'));
    }
    #[Route('/publications/{id<[0-9]+>}', name: 'app_publication_show')]

    public function show(Publication $publication): Response
    {
        return $this->render('publications/show.html.twig', compact('publication'));
    }
}
