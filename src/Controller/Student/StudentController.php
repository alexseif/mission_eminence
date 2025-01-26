<?php

namespace App\Controller\Student;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/student')]
#[IsGranted('ROLE_STUDENT')]
class StudentController extends AbstractController
{
    #[Route('/', name: 'student_home')]
    public function index(): Response
    {
        return $this->render('student/index.html.twig');
    }

    #[Route('/events/{id}', name: 'student_event_show', methods: ['GET'])]
    public function show(Event $event): JsonResponse
    {
        $html = $this->renderView('student/event/_card.html.twig', [
            'event' => $event,
        ]);

        return new JsonResponse([
            'eventTitle'=>$event->getTitle(),
            'content' => $html
        ]);
    }
}
