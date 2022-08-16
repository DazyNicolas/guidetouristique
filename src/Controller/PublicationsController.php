<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PublicationRepository;

class PublicationsController extends AbstractController
{
    #[Route('/publications', name: 'app_publications')]

    public function index(PublicationRepository $publicationsRepository): Response
    {

       // dump($publicationsRepository ->findAll());
       $publications = $publicationsRepository -> findAll();
        return $this->render('publications/index.html.twig', compact('publications'));
    }
}
