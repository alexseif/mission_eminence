<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();
        
        // Check if user has admin role
        if (in_array('ROLE_ADMIN', $token->getRoleNames(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('admin_home'));
        }
        
        // Default to student dashboard
        return new RedirectResponse($this->urlGenerator->generate('student_home'));
    }
} 