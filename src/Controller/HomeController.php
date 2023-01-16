<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Services\MailerService;
use App\Repository\TemoignagesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(TemoignagesRepository $temoignagesRepository, Request $request, MailerService $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $contact = $form->handleRequest($request);
        $url = $this->generateUrl('app_home', [
            '_fragment' => 'contact'
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $contact->getData();
            $content = nl2br($contactFormData['message']);
            $mailer->sendEmail(
                $contactFormData['email'], 
                $contactFormData['nom'], 
                'emails/contact.html.twig', 
                $contactFormData['sujet'], 
                $content
            );

            $this->addFlash('primary', 'Votre message a bien été envoyé<br>Nous allons vous répondre dans les meilleurs délais');
            return $this->redirect($url);
        }
        
        return $this->render('home/index.html.twig', [
            'temoignages' => $temoignagesRepository->returnTemoignages(),
            'contactForm' => $form->createView()
        ]);
    }
}
