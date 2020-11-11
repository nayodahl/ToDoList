<?php

namespace App\Controller;

use Anyx\LoginGateBundle\Service\BruteForceChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils, BruteForceChecker $bruteForceChecker)
    {
        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();

        $request->request->set('_username', $lastUsername);

        if (!$bruteForceChecker->canLogin($request)) {
            $this->addFlash('error', 'Trop de tentative de connexion, veuillez attendre 10 min et réessayer');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/login_check", name="login_check")
     * @codeCoverageIgnore
     */
    public function loginCheck()
    {
        // This code is never executed.
    }

    /**
     * @Route("/logout", name="logout")
     * @codeCoverageIgnore
     */
    public function logoutCheck()
    {
        // This code is never executed.
    }
}
