<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/calendar')]
class CalendarController extends AbstractController
{
    #[Route('/', name: 'admin_calendar_index')]
    public function index(): Response
    {
        return $this->render('admin/calendar/index.html.twig', [
            'controller_name' => 'CalendarController',
        ]);
    }
    #[Route('/api/events', name: 'calendar_api_events')]
    public function events(EventRepository $eventRepository): JsonResponse
    {
        $events = $eventRepository->findAll();
        $eventArray = [];
        foreach ($events as $event) {
            $eventArray[] = [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'start' => $event->getStart()->format('Y-m-d'),
                'end' => $event->getEnd()->format('Y-m-d'),
                'description' => $event->getDescription(),
                'url' => $this->generateUrl('admin_event_show', ['id' => $event->getId()])

            ];
        }
        return new JsonResponse($eventArray);
    }
}
