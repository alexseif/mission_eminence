<?php

namespace App\Controller\Student;

use App\Entity\Partnership;
use App\Form\PartnershipType;
use App\Repository\PartnershipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_STUDENT')]
#[Route('/student/partnership')]
final class PartnershipController extends AbstractController
{
    #[Route(name: 'student_partnership_index', methods: ['GET'])]
    public function index(PartnershipRepository $partnershipRepository): Response
    {
        return $this->render('student/partnership/index.html.twig', [
            'partnerships' => $partnershipRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'student_partnership_show', methods: ['GET'])]
    public function show(Partnership $partnership): Response
    {
        return $this->render('student/partnership/show.html.twig', [
            'partnership' => $partnership,
        ]);
    }
}
