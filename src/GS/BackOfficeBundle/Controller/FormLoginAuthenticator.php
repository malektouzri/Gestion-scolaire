<?php
/**
 * Created by PhpStorm.
 * User: ACER
 * Date: 20/06/2018
 * Time: 11:44
 */

namespace GS\BackOfficeBundle\Controller;


use Doctrine\ORM\EntityManager;
use GS\BackOfficeBundle\Entity\Ecole;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class FormLoginAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $jwtEncoder;

    public function __construct(EntityManager $em, JWTEncoder $jwtEncoder)
    {
        $this->em = $em;
        $this->jwtEncoder = $jwtEncoder;
    }

    public function getCredentials(Request $request)
    {
      $exractor = new AuthorizationHeaderTokenExtractor('Bearer','Authorization');
      $token = $exractor->extract($request);
      if ($token == false){
          return;
        }
        return $token;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $data = $this->jwtEncoder->decode($credentials);
        if(!$data){
            return;
        }
        $username = $data['username'];
        return $this->em->getRepository('BackOfficeBundle:Ecole')->findOneBy(array('username'=>$username));
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(['message'=>$exception->getMessageKey()],401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
return;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $url = $this->router->generate('_login');

        return new RedirectResponse($url);
    }

    public function supportsRememberMe()
    {

    }

}