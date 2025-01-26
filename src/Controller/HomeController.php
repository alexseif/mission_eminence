<?php

namespace App\Controller;

use App\Repository\TestimonialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    public function indexRedirect(): Response
    {
        return $this->redirectToRoute('app_home', ['_locale' => 'en']);
    }

    #[Route('/', name: 'app_home')]
    public function index(TestimonialRepository $testimonialRepository, Request $request): Response
    {
        // This ensures the locale is set properly
        $request->setLocale($request->getLocale());

        return $this->render('home/index.html.twig', [
            'testimonials' => $testimonialRepository->findAll()
        ]);
    }
}
