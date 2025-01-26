<?php

namespace App\Controller\Student;

use App\Entity\MMM;
use App\Form\MMMType;
use App\Repository\MMMRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_STUDENT')]
#[Route('/student/mmm')]
final class MMMController extends AbstractController
{
    #[Route(name: 'student_mmm_index', methods: ['GET'])]
    public function index(MMMRepository $mMMRepository): Response
    {
        return $this->render('student/mmm/index.html.twig', [
            'mmms' => $mMMRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'student_mmm_show', methods: ['GET'])]
    public function show(MMM $mMM): Response
    {
        return $this->render('student/mmm/show.html.twig', [
            'mmm' => $mMM,
        ]);
    }
}
