<?php

namespace App\Controller\Admin;

use App\Repository\CourseRepository;
use App\Repository\UserRepository;
use App\Repository\TestimonialRepository;
use App\Repository\PartnershipRepository;
use App\Repository\MMMRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_home')]
    public function index(
        UserRepository $userRepository,
        CourseRepository $courseRepository,
        TestimonialRepository $testimonialRepository,
        PartnershipRepository $partnershipRepository,
        MMMRepository $mmmRepository,
        EventRepository $eventRepository
    ): Response {
        $stats = [
            'students' => $userRepository->countStudents(),
            'courses' => $courseRepository->count([]),
            'testimonials' => $testimonialRepository->count([]),
            'partnerships' => $partnershipRepository->count([]),
            'mmm' => $mmmRepository->count([]),
            'events' => $eventRepository->count([])
        ];

        return $this->render('admin/index.html.twig', [
            'stats' => $stats
        ]);
    }
}
