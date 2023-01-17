<?php

namespace App\Controller\Admin;

use App\Repository\UsersRepository;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\BookingRepository;
use App\Repository\FacturesRepository;
use App\Repository\TemoignagesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("/admin")
*/
class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="app_admin_dashboard_index", methods={"GET"})
     */
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        UsersRepository $usersRepository, 
        FacturesRepository $facturesRepository,
        BookingRepository $bookingRepository,
        TemoignagesRepository $temoignagesRepository, 
        ChartBuilderInterface $chartBuilder
    ): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_PIE);

        $chart->setData([
            'datasets' => [
                [
                'data' => [300, 50, 100,300, 50, 100,300, 50, 100,300, 50, 100],
                'backgroundColor' => ['#90CAF9','#64B5F6','#42A5F5','#2196F3','#1E88E5','#1976D2','#1565C0','#0D47A1','#82B1FF','#448AFF','#2979FF','#2962FF'],
                'hoverOffset' => 4
                ],
            ],
        ]);

        
        //$factures = $facturesRepository->findBy(['client' => $this->getUser()]);
        return $this->render('dashboard/admin/index.html.twig', [
            'clients' => $usersRepository->count([]),
            'factures' => $facturesRepository->count([]),
            'chart' => $chart,

        ]);
    }
}
