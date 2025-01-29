<?php

namespace App\Controller\Student;

use App\Entity\Course;
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

    #[Route("/test", name: 'student_course_certificate_test')]
    public function test(PdfGenerator $pdfGenerator)
    {
        //TODO: generate certificate per course
        //TODO: make student attend course
        //TODO: issue certificate on course completion
        $date = new \DateTime();
        $data = [
            'studentName' => "Student Name",
            'courseName' => "Course Name",
            'completedDate' => $date->format('Y-m-d'),
            'templatePath' => $this->getParameter('kernel.project_dir') . '/public/pdf/course_x_template.pdf'
        ];

        $pdfGenerator->generatePdf($data);

        // Assign the PDF to the graduated student
        // $student->setCertificate($templatePath);
        return new Response();
    }

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

        if ($course->isLocked()) {
            $this->addFlash('error', 'This course is currently locked.');
            return $this->redirectToRoute('student_course_index');
        }

        $user = $this->getUser();
        if ($course->isStudentEnrolled($user)) {
            $this->addFlash('warning', 'You are already enrolled in this course.');
            return $this->redirectToRoute('student_course_show', ['id' => $course->getId()]);
        }

        $course->addStudent($user);
        $entityManager->flush();

        $this->addFlash('success', 'You have successfully enrolled in the course.');
        return $this->redirectToRoute('student_course_show', ['id' => $course->getId()]);
    }
}
