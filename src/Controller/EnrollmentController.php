<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/enroll')]
class EnrollmentController extends AbstractController
{
    #[Route('/', name: 'app_enrollment_enroll')]
    public function enroll(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        CourseRepository $courseRepository,
        TranslatorInterface $translator
    ): Response {

        $studentId = $request->request->get('student') ?? null;
        $courseId = $request->request->get('course') ?? null;
        if ($studentId === null || $courseId === null) {
            $this->addFlash('error', $translator->trans('Invalid request'));
            return $this->redirect($this->generateUrl('admin_user_show', ['id' => $studentId]));
        }

        $student = $userRepository->find($studentId);
        $course = $courseRepository->find($courseId);
        $student->addCourse($course);
        $entityManager->persist($student);
        $entityManager->flush();

        $this->addFlash('success', $translator->trans('Student enrolled successfully!'));
        return $this->redirect($this->generateUrl('admin_user_show', ['id' => $studentId]));
    }

    #[Route('/remove', name: 'app_enrollment_remove')]
    public function remove(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        CourseRepository $courseRepository,
        TranslatorInterface $translator
    ): Response {
        $studentId = $request->request->get('student') ?? null;
        $courseId = $request->request->get('course') ?? null;
        if ($studentId === null || $courseId === null) {
            $this->addFlash('error', $translator->trans('Invalid request'));
            return $this->redirect($this->generateUrl('admin_user_show', ['id' => $studentId]));
        }

        $student = $userRepository->find($studentId);
        $course = $courseRepository->find($courseId);
        $course->removeStudent($student);
        $entityManager->flush();

        $this->addFlash('success', $translator->trans('Student removed successfully!'));
        return $this->redirect($this->generateUrl('admin_user_show', ['id' => $studentId]));
    }
}
