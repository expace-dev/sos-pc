<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Factures;
use Konekt\PdfInvoice\InvoicePrinter;
use App\Repository\FacturesRepository;
use App\Form\Compta\Admin\FacturesType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("/admin/factures")
*/
class FacturesController extends AbstractController
{
    /**
    * @Route("/", name="app_admin_factures_index", methods={"GET"})
    */
    public function index(FacturesRepository $facturesRepository): Response
    {
        $factures = $facturesRepository->findBy([], ['date' => 'DESC']);
        return $this->render('factures/index.html.twig', [
            'factures' => $factures,
        ]);
    }

    /**
    * @Route("/new", name="app_admin_factures_new", methods={"GET", "POST"})
    */
    public function new(Request $request, FacturesRepository $facturesRepository): Response
    {
        $facture = new Factures();
        $form = $this->createForm(FacturesType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            

            $facture->setStatut('en_attente');
            $facture->setCreatedAt(time());
            $facture->setDate(new DateTime());

            if ($facture->getTitre() === 'Dépannage à distance') {
                $facture->setAmount(29);
            }
            else {
                $facture->setAmount(49);
            }

            $invoice = new InvoicePrinter('A4', '€', 'fr');

            $numFact = $facturesRepository->nbreFact()+1;

            if (strlen($numFact) == 1) {
                $fact = 'FAC00000000' .$numFact;
            }
            elseif (strlen($numFact) == 2) {
                $fact = 'FAC0000000' .$numFact;
            }
            elseif (strlen($numFact) == 3) {
                $fact = 'FAC000000' .$numFact;
            }
            elseif (strlen($numFact) == 4) {
                $fact = 'FAC00000' .$numFact;
            }
            elseif (strlen($numFact) == 5) {
                $fact = 'FAC0000' .$numFact;
            }
            elseif (strlen($numFact) == 6) {
                $fact = 'FAC000' .$numFact;
            }
            elseif (strlen($numFact) == 7) {
                $fact = 'FAC00' .$numFact;
            }
            elseif (strlen($numFact) == 8) {
                $fact = 'FAC0' .$numFact;
            }
            elseif (strlen($numFact) == 9) {
                $fact = 'FAC' .$numFact;
            }

            $url = 'documents_clients/' . $fact . '.pdf';

            $facture->setUrl($url);
            $facture->setSlug($fact);

            $invoice->setLogo("images/logo_documents.jpg");
            $invoice->setColor("#0083c3");      // pdf color scheme
            $invoice->setType("FACTURE");    // Invoice Type
            $invoice->setReference($fact);   // Reference
            $invoice->setDate(date('d m Y',time()));   //Billing Date
            $invoice->setFrom(array(
                "Sos Home PC",
                "Mr HUSSON Frédéric",
                "121 Rue Maurice Burrus",
                "68160 Ste Croix Aux Mines",
                "Téléphone: 07 54 27 07 77",
                "E-mail: contact@sos-home-pc.fr",
                "Site web: https://www.sos-home-pc.fr",
                "RCS: Colmar TI 482 176 740",
                "Siret: 48217674000029",
                "APE: 722C"
            ));
            if ($facture->getClient()->getSociete() === null) {
                $invoice->setTo([
                    $facture->getClient()->getNom(). ' ' .$facture->getClient()->getPrenom(),
                    $facture->getClient()->getAdresse(),
                    $facture->getClient()->getCodePostal(). ' ' .$facture->getClient()->getVille(),
                    strtoupper($facture->getClient()->getPays()),
                ]);
            }
            else {
                $invoice->setTo([
                    $facture->getClient()->getSociete(),
                    $facture->getClient()->getNom(). ' ' .$facture->getClient()->getPrenom(),
                    $facture->getClient()->getAdresse(),
                    $facture->getClient()->getCodePostal(). ' ' .$facture->getClient()->getVille(),
                    strtoupper($facture->getClient()->getPays()),
                ]);
            }

            $invoice->addItem(
                $facture->getTitre(), 
                $facture->getContenu(), 
                1, 
                false, 
                false, 
                false, 
                $facture->getAmount()
            );

            $invoice->addTotal("Total", $facture->getAmount(), true);

            $invoice->addTitle("Informations");
            $invoice->addParagraph("TVA non applicable art. 293B du CGI");
            
            $invoice->setFooternote("Sos Home PC");

            $invoice->render($url,'F');

            
            $facturesRepository->add($facture, true);

            $this->addFlash(
                'success',
                "La facture a été enregistré avec succès"
            );

            return $this->redirectToRoute('app_admin_factures_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('factures/new.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }


    /**
    * @Route("/{slug}", name="app_admin_factures_show", methods={"GET"})
    */
    public function show(Factures $facture)
    {

        $mime = "application/pdf";
        $fichier = $facture->getUrl();

            header('Content-type: ' . $mime);
            readfile($fichier);
    }
    
}
