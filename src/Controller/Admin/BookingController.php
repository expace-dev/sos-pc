<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Booking;
use App\Repository\UsersRepository;
use App\Form\Booking\Admin\EditType;
use App\Repository\BookingRepository;
use App\Form\Booking\Admin\BookingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/booking")
 */
class BookingController extends AbstractController
{
    /**
     * @Route("/calendar", name="app_admin_booking_calendar", methods={"GET"})
     */
    public function calendar(): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        
        return $this->renderForm('booking/admin/calendar.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/", name="app_admin_booking_index", methods={"GET"})
     */
    public function index(BookingRepository $bookingRepository): Response
    {
        return $this->render('booking/admin/index.html.twig', [
            'bookings' => $bookingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_admin_booking_new", methods={"PUT"})
     */
    public function new(Request $request, ?Booking $booking, UsersRepository $usersRepository, EntityManagerInterface $manager, BookingRepository $bookingRepository): Response
    {
        // On récupère les données
        $donnees = json_decode($request->getContent());

        $start = new DateTime($donnees->params->start);
        $end = new DateTime($donnees->params->end);


        // Ont vérifie qu'il n'y a pas un rendez vous entre la date de départ et de fin
        $rdv = $bookingRepository
            ->createQueryBuilder('rdv')
            ->select('COUNT(rdv)')
            ->where('rdv.beginAt BETWEEN :start and :end OR rdv.endAt BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getSingleScalarResult()
        ;

        if ($rdv>0) {
            $message = "Cette plage horraire n'est pas disponible";
            $code = "404";
        }
        elseif ($start->format('H:i') > "20:00" OR $start->format('H:i') < "09:00" OR $end->format('H:i') > "20:00" OR $end->format('H:i') < "09:00") {
            $message = "Nos services sont fermé à cette heure";
            $code = "403";
        }
        else {

            $booking = new Booking;


            $booking->setTitle($donnees->params->title);
            $booking->setDescription($donnees->params->description);
            $booking->setBeginAt(new DateTime($donnees->params->start));
            $booking->setEndAt(new DateTime($donnees->params->end));
            $user = $usersRepository->findOneBy(
                [
                    'id'=> $donnees->params->user
                ]
            );
            $booking->setClient($user);

            $manager->persist($booking);
            $manager->flush();

            $message = "Cette plage horraire est disponible";
            $code = "200";
        }

        


            return $this->json(['code' => $code, 'message' => $message], $code);
    }

    /**
     * @Route("/new/erreur", name="app_admin_booking_error", methods={"GET"})
     */
    public function errorCreat(): Response
    {

        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        $this->addFlash(
            'danger', 
            'Cette plage horraire est déjà prise, veuillez modifier votre saisie !!'
        );

        return $this->renderForm('booking/admin/calendar.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new/erreur/fermeture", name="app_admin_booking_fermeture", methods={"GET"})
     */
    public function errorFermeture(): Response
    {

        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        $this->addFlash(
            'danger', 
            'Nos services sont fermé à cette heure, veuillez modifier votre saisie !'
        );

        return $this->renderForm('booking/admin/calendar.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new/confirmation", name="app_admin_booking_valid", methods={"GET"})
     */
    public function validCreat(): Response
    {

        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        $this->addFlash(
            'success', 
            'Votre demande d\'intervention est validée'
        );

        return $this->renderForm('booking/admin/calendar.html.twig', [
            'form' => $form,
        ]);
    }
    
    /**
     * @Route("/{id}/edit", name="app_admin_booking_edit", methods={"PUT"})
     */
    public function edit(Request $request, Booking $booking, EntityManagerInterface $manager): Response
    {

        $donnees = json_decode($request->getContent());

        $booking->setBeginAt(new DateTime($donnees->params->start));
        $booking->setEndAt(new DateTime($donnees->params->end));

        $manager->persist($booking);
        $manager->flush();


        return $this->json(['code' => 200, 'message' => 'Modification'], 200);

    }

    /**
     * @Route("/{id}/edition", name="app_admin_booking_edition", methods={"POST", "GET"})
     */
    public function edition(Request $request, Booking $booking, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(EditType::class, $booking);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            
            $manager->flush();

            $this->addFlash(
                'success',
                "Cette intervention a bien été modifié"
            );

            return $this->redirectToRoute('app_admin_booking_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('booking/admin/edit.html.twig', [
            'booking' => $booking,
            'form' => $form,
        ]);




    }

    /**
     * @Route("/delete/{id}", name="app_admin_booking_delete", methods={"GET"})
     */
    public function delete(Request $request, Booking $booking, BookingRepository $bookingRepository): Response
    {
        $bookingRepository->remove($booking, true);

        $this->addFlash(
            'warning',
            "Cette intervention a bien été supprimé"
        );

        return $this->redirectToRoute('app_admin_booking_index', [], Response::HTTP_SEE_OTHER);
    }
}
