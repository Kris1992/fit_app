<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Routing\RouterInterface;
use App\Repository\UserRepository;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;


class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{

    const ATTEMPTS_LIMIT = 5;

    use TargetPathTrait;

    private $userRepository;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $router;
    private $em;

    public function __construct(UserRepository $userRepository, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder, RouterInterface $router, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->router = $router;
        $this->em = $em;
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        return $this->userRepository->findOneBy(['email' => $credentials['email']]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if($user->getFailedAttempts() < self::ATTEMPTS_LIMIT)
        {
            $isPasswordValid = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
            if(!$isPasswordValid)
            {

                $failedAttempts = $user->increaseFailedAttempts();
                $this->em->persist($user);
                $this->em->flush();

                throw new CustomUserMessageAuthenticationException(
                    sprintf('It is your %d failed attempt to log on from %d available ', $failedAttempts, self::ATTEMPTS_LIMIT)
                );

            }
            else
            {
                $user->resetFailedAttempts();
                $this->em->persist($user);
                $this->em->flush();
            }
            return $isPasswordValid; 
        }
        else{
             
            throw new CustomUserMessageAuthenticationException(sprintf('Account blocked due to %d failed logon attempts! Unlock by forgot password section', self::ATTEMPTS_LIMIT));
        }
       
    }

    /*public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
       
    }*/

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('app_homepage'));
    }

    /*public function start(Request $request, AuthenticationException $authException = null)
    {
        // todo
    }

    public function supportsRememberMe()
    {
        // todo
    }*/

    protected function getLoginUrl()
    {   
        return $this->router->generate('app_login');
    }
}
