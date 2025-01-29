<?php

namespace App\Controller\Student;

use App\Entity\Course;
use App\Entity\User;
use App\Entity\CourseCompletion;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Service\PdfGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_STUDENT')]
#[Route('/student/course')]
final class CourseController extends AbstractController
{
    #[Route(name: 'student_course_index', methods: ['GET'])]
    public function index(CourseRepository $courseRepository): Response
    {
        return $this->render('student/course/index.html.twig', [
            'courses' => $courseRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'student_course_show', methods: ['GET'])]
    public function show(Course $course): Response
    {
        $user = $this->getUser();

        if (!$course->isStudentEnrolled($user)) {
            $this->addFlash('error', 'You are not enrolled in this course.');
            return $this->redirectToRoute('student_course_index');
        }

        return $this->render('student/course/show.html.twig', [
            'course' => $course,
            'isEnrolled' => true,
        ]);
    }

    #[Route('/{id}/enroll', name: 'student_course_enroll', methods: ['POST'])]
    public function enroll(
        Course $course,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        if (!$this->isCsrfTokenValid('enroll' . $course->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('student_course_index');
        }

        /** @var User $user */
        $user = $this->getUser();
        
        if ($user->isEnrolledInCourse($course)) {
            $this->addFlash('warning', 'You are already enrolled in this course.');
            return $this->redirectToRoute('student_course_show', ['id' => $course->getId()]);
        }

        $enrollment = $course->addStudent($user);
        $entityManager->persist($enrollment);
        $entityManager->flush();

        $this->addFlash('success', 'You have successfully enrolled in the course.');
        return $this->redirectToRoute('student_course_show', ['id' => $course->getId()]);
    }

    #[Route('/{id}/complete', name: 'student_course_complete', methods: ['POST'])]
    public function completeCourse(
        Course $course,
        Request $request,
        PdfGenerator $pdfGenerator,
        EntityManagerInterface $entityManager,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        
        $enrollment = $user->getEnrollmentForCourse($course);
        if (!$enrollment) {
            throw $this->createAccessDeniedException('You are not enrolled in this course.');
        }

        if ($enrollment->isCompleted()) {
            return $this->json([
                'success' => false,
                'message' => 'Course already completed'
            ]);
        }

        // Generate certificate
        $certificatePath = sprintf(
            '%s/public/certificates/%d_%d.pdf',
            $this->getParameter('kernel.project_dir'),
            $user->getId(),
            $course->getId()
        );
        
        $pdfGenerator->generateCourseCertificate([
            'studentName' => $user->getFullName(),
            'courseName' => $course->getTitle(),
            'completionDate' => new \DateTime(),
            'outputPath' => $certificatePath
        ]);
        
        $enrollment->complete();
        $enrollment->setCertificatePath(basename($certificatePath));
        $entityManager->flush();
        
        return $this->json([
            'success' => true,
            'certificateUrl' => '/certificates/' . basename($certificatePath)
        ]);
    }
}
