<?php

namespace App\Controller\Client;

use App\Entity\Factures;
use App\Repository\FacturesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/client/factures")
 */
class FacturesController extends AbstractController
{
    /**
     * @Route("/", name="app_factures_index", methods={"GET"})
     */
    public function index(FacturesRepository $facturesRepository): Response
    {
        $factures = $facturesRepository->findBy(['client' => $this->getUser()], ['date' => 'DESC']);
        
        return $this->render('factures/index.html.twig', [
            'factures' => $factures,
        ]);
    }

    /**
     * @Route("/{slug}", name="app_factures_show", methods={"GET"})
     */
    public function show(Factures $facture)
    {

        $mime = "application/pdf";
        $fichier = $facture->getUrl();

        if ($facture->getClient() === $this->getUser()) {
            header('Content-type: ' . $mime);
            readfile($fichier);
        }
        else {
            throw new AccessDeniedException("Vous n'avez pas l'autorisation d'accéder à cette page");
        }
    }
}
