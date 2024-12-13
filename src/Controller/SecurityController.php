<?php

namespace App\Controller;

use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class SecurityController extends AbstractController
{
    private $resetPasswordHelper;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    //Create reset password using composer require symfonycasts/reset-password-bundle
    #[Route('reset-password', name: 'app_reset_password_request')]
    public function request(Request $request, UserRepository $userRepository, MailerInterface $mailer): Response
    {
        // TODO: Check requirement for symfonycasts/reset-password-bundle for doctrine objects and the form for this controller
        $email = $request->request->get('email');

        $user = $userRepository->findOneByEmail($email);

        if (!$user) {
            return new Response('Email not found', 404);
        }

        $token = $this->resetPasswordHelper->generateResetToken($user);

        $email = (new Email())
            ->from('your_email@example.com')
            ->to($user->getEmail())
            ->subject('Reset Password')
            ->html($this->renderView('security/reset_password_request.html.twig', ['user' => $user, 'url' => $this->generateUrl('reset_password_change', ['token' => $token])]));

        // Send the email
        $mailer->send($email);

        return new Response('Email sent', 200);
    }
    #[Route('/change/{token}', name: 'app_reset_password_change')]
    public function change(Request $request, string $token, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneByResetPasswordToken($token);

        if (!$user) {
            return new Response('Invalid token', 404);
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setResetPasswordToken(null);
            $user->setPassword($form->get('password')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('security/reset_password_change.html.twig', ['form' => $form->createView()]);
    }
}
