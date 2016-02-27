<?php
// src/AppBundle/Form/DataTransformer/IssueToNumberTransformer.php
namespace Sopinet\ChatBundle\Form\DataTransformer;

use AppBundle\Services\LoginHelper;
use Sopinet\ChatBundle\Model\UserInterface as User;
use Sopinet\ChatBundle\Entity\Device;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Exception\Exception;

/**
 * Transform from timestamp to datetime
 * Reverse transform from datetime to timestamp
 *
 * Class LoginTransformer
 * @package Sopinet\ChatBundle\Form\DataTransformer
 */
class LoginMultipleTransformer implements DataTransformerInterface
{
    private $container;
    private $em;
    private $request;
    private $users;

    public function __construct(Container $container, Request $request, Device $device)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
        $this->request = $request;
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        foreach($device->getUser() as $user) {
            $this->users->add($user);
        }
    }

    public function transform($user)
    {
        if (null === $user) {
            return '';
        }
        return $user;
    }


    public function reverseTransform($userNull)
    {
        // Auth User, Check and Set
        /** @var LoginHelper $loginHelper */
        $loginHelper = $this->container->get('sopinet_login_helper');
        /** @var User $user */
        try {
            $user = $loginHelper->getUser($this->request);
        } catch(Exception $e) {
            throw new TransformationFailedException(sprintf(
                'User is required',
                -1
            ));
        }

        // Si el usuario ya existía en el dispositivo, no lo añadimos
        foreach($this->users as $u) {
            if ($user->getId() == $u->getId()) {
                return $this->users;
            }
        }

        // Si no existía lo añadimos
        $this->users->add($user);
        return $this->users;
    }
}