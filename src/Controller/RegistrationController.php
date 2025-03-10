<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelper;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        FormLoginAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        VerifyEmailHelperInterface $verifyEmailHelper,
        MailerInterface $mailer,
        UserRepository $userRepository
    ): Response {
        $src = $request->get('src');
        dump($src);
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        if ('free-course' == $src) {
            $form->add('referredBy', TextType::class, [
                'label' => 'Referred By',
                'attr' => ['placeholder' => 'Enter your referral'],
                'required' => false
            ]);
        } else {
            $form->add('iGeniusUserID', TextType::class, [
                'label' => 'iGenius User ID',
                'attr' => ['placeholder' => 'Enter your iGenius User ID'],
                'required' => false
            ])
                ->add('nameOfEnroller', TextType::class, [
                    'label' => 'Name of Enroller',
                    'attr' => ['placeholder' => 'Enter your name of enroller'],
                    'required' => false
                ]);
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $existingUser = $userRepository->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser) {
                $form->get('email')->addError(new FormError('This email is already in use.'));
            } else {
                // encode the plain password 
                $user->setPassword($passwordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
                $user->setRoles(['ROLE_STUDENT']);
                $entityManager->persist($user);
                $entityManager->flush();


                // Send a verification email
                $signatureComponents = $verifyEmailHelper->generateSignature(
                    'app_verify_email',
                    $user->getId(),
                    $user->getEmail()
                );

                $email = (new Email())
                    ->from('your_email@example.com')
                    ->to($user->getEmail())
                    ->subject('Verify your email address')
                    ->html('<p>Click on the link to verify your email address: <a href="' . $signatureComponents->getSignedUrl() . '">Verify</a></p>');

                $mailer->send($email);


                // auto-login the user after registration 
                return $userAuthenticator->authenticateUser($user, $authenticator, $request);
            }
        }
        return $this->render('registration/register.html.twig', ['registrationForm' => $form->createView(),]);
    }

    #[Route('/verify-email', name: 'app_verify_email')]
    public function verifyEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($this->getUser()->getId());
        $verifyEmailHelper->validateEmailConfirmationFromRequest($request, $user->getId(), $user->getEmail());

        return $this->redirectToRoute('app_home');
    }
}
