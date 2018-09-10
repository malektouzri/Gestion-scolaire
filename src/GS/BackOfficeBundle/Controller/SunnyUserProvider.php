<?php
/**
 * Created by PhpStorm.
 * User: ACER
 * Date: 20/06/2018
 * Time: 12:05
 */

namespace GS\BackOfficeBundle\Controller;


use GS\BackOfficeBundle\Entity\Ecole;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SunnyUserProvider implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $user = $this->em->getRepository('BackOfficeBundle:Ecole')->findOneBy(array('username'=>$username));
        if (!$user){
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    public function supportsClass($class)
    {
        return $class == 'GS\BackOfficeBundle\Controller\Entity\Ecole';
    }

}