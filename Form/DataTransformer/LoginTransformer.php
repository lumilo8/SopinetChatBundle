<?php
// src/AppBundle/Form/DataTransformer/IssueToNumberTransformer.php
namespace Sopinet\ChatBundle\Form\DataTransformer;

use AppBundle\Services\LoginHelper;
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
class LoginTransformer implements DataTransformerInterface
{
    private $container;
    private $em;
    private $request;

    public function __construct(Container $container, Request $request)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
        $this->request = $request;
    }

    public function transform($user)
    {
        if (null === $user) {
            return '';
        }

        return $user->__toString();
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

        return $user;
    }
}