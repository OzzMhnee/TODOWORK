<?php

namespace App\Security;


use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    public const LOGIN_ROUTE = 'app_login';

    private RouterInterface $router;
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(RouterInterface $router, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('_username', '');

        $request->getSession()->set('_last_username', $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('_password', '')),
            [
                new CsrfTokenBadge(
                    'authenticate',
                    $request->request->get('_csrf_token'),
                    // $this->csrfTokenManager // <-- injection explicite du token manager
                ),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?RedirectResponse
    {
        return new RedirectResponse($this->router->generate('app_home_page'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate(self::LOGIN_ROUTE);
    }
}
