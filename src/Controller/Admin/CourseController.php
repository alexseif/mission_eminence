<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Entity\User;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Service\PdfGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/course')]
final class CourseController extends AbstractController
{

    #[Route(name: 'admin_course_index', methods: ['GET'])]
    public function index(CourseRepository $courseRepository): Response
    {
        $courses = $courseRepository->findAll();
        return $this->render('admin/course/index.html.twig', [
            'courses' => $courses,
        ]);
    }

    #[Route('/new', name: 'admin_course_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($course);
            $entityManager->flush();

            return $this->redirectToRoute('admin_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/course/new.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_course_show', methods: ['GET'])]
    public function show(Course $course): Response
    {
        return $this->render('admin/course/show.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_course_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/course/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_course_delete', methods: ['POST'])]
    public function delete(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $course->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($course);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_course_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{courseId}/complete/{userId}', name: 'admin_course_complete_for_user', methods: ['POST'])]
    public function completeForUser(
        int $courseId,
        int $userId,
        Request $request,
        EntityManagerInterface $entityManager,
        PdfGenerator $pdfGenerator
    ): Response {
        $course = $entityManager->getRepository(Course::class)->find($courseId);
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$course || !$user) {
            throw $this->createNotFoundException('Course or User not found');
        }

        if (!$this->isCsrfTokenValid('complete-course' . $courseId . $userId, $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        $enrollment = $user->getEnrollmentForCourse($course);

        if (!$enrollment) {
            // Create enrollment if it doesn't exist
            $enrollment = $course->addStudent($user);
            $entityManager->persist($enrollment);
        }

        if (!$enrollment->isCompleted()) {
            // Generate certificate
            $certificatePath = sprintf(
                '%s/public/certificates/%d_%d.pdf',
                $this->getParameter('kernel.project_dir'),
                $user->getId(),
                $course->getId()
            );
            
            $templatePath = null;
            if ($course->getCertificateTemplate()) {
                $templatePath = $this->getParameter('kernel.project_dir') . '/public/uploads/certificates/templates/' . $course->getCertificateTemplate();
            }
            
            $pdfGenerator->generateCourseCertificate([
                'studentName' => $user->getFullName(),
                'courseName' => $course->getTitle(),
                'completionDate' => new \DateTime(),
                'outputPath' => $certificatePath,
                'templatePath' => $templatePath,
            ]);

            $enrollment->complete();
            $enrollment->setCertificatePath(basename($certificatePath));
            $entityManager->flush();

            $this->addFlash('success', sprintf(
                'Course "%s" has been marked as completed for user "%s"',
                $course->getTitle(),
                $user->getFullName()
            ));
        } else {
            $this->addFlash('warning', 'Course is already completed for this user');
        }

        return $this->redirectToRoute('admin_user_show', ['id' => $userId]);
    }
}
