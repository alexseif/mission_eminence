<?php

namespace App\Controller\Admin;

use App\Entity\MMM;
use App\Form\MMMType;
use App\Repository\MMMRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/mmm')]
final class MMMController extends AbstractController
{
    #[Route(name: 'admin_mmm_index', methods: ['GET'])]
    public function index(MMMRepository $mMMRepository): Response
    {
        return $this->render('admin/mmm/index.html.twig', [
            'mmms' => $mMMRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_mmm_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mMM = new MMM();
        $form = $this->createForm(MMMType::class, $mMM);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($mMM);
            $entityManager->flush();

            return $this->redirectToRoute('admin_mmm_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/mmm/new.html.twig', [
            'mmm' => $mMM,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_mmm_show', methods: ['GET'])]
    public function show(MMM $mMM): Response
    {
        return $this->render('admin/mmm/show.html.twig', [
            'mmm' => $mMM,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_mmm_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MMM $mMM, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MMMType::class, $mMM);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_mmm_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/mmm/edit.html.twig', [
            'mmm' => $mMM,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_mmm_delete', methods: ['POST'])]
    public function delete(Request $request, MMM $mMM, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $mMM->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($mMM);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_mmm_index', [], Response::HTTP_SEE_OTHER);
    }
}
