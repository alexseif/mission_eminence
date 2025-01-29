<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class EnrollmentController extends AbstractController
{
    #[Route('/enrollment/enroll', name: 'app_enrollment_enroll', methods: ['POST'])]
    public function enroll(Request $request, EntityManagerInterface $entityManager): Response
    {
        $courseId = $request->request->get('course');
        $studentId = $request->request->get('student');

        $course = $entityManager->getRepository(Course::class)->find($courseId);
        $student = $entityManager->getRepository(User::class)->find($studentId);

        if (!$course || !$student) {
            throw $this->createNotFoundException('Course or Student not found');
        }

        if (!$student->isEnrolledInCourse($course)) {
            $enrollment = $course->addStudent($student);
            $entityManager->persist($enrollment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_user_show', ['id' => $studentId]);
    }

    #[Route('/enrollment/remove', name: 'app_enrollment_remove', methods: ['POST'])]
    public function remove(Request $request, EntityManagerInterface $entityManager): Response
    {
        $courseId = $request->request->get('course');
        $studentId = $request->request->get('student');

        $course = $entityManager->getRepository(Course::class)->find($courseId);
        $student = $entityManager->getRepository(User::class)->find($studentId);

        if (!$course || !$student) {
            throw $this->createNotFoundException('Course or Student not found');
        }

        $enrollment = $student->getEnrollmentForCourse($course);
        if ($enrollment) {
            $entityManager->remove($enrollment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_user_show', ['id' => $studentId]);
    }
}
