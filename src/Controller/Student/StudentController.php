<?php

namespace App\Controller\Student;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_STUDENT')]
#[Route('/course')]
class StudentController extends AbstractController
{
    #[Route('/', name: 'student_home')]
    public function index(): Response
    {
        return $this->render('student/index.html.twig', []);
    }

}
