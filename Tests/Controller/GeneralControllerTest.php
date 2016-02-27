<?php

namespace Sopinet\ChatBundle\Tests\Controller;

use Sopinet\ChatBundle\Service\InterfaceHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;

class GeneralControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /** @var  Container */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->container = static::$kernel->getContainer();
        $this->em = $this->container
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Crea dos usuarios
     * Crea un chat con dichos usuarios
     * Comprueba que la creación se realiza con éxito
     */
    public function testCreateChat()
    {
        $userManager = $this->container->get('fos_user.user_manager');

        /** @var User $user1 */
        $user1 = $userManager->createUser();

        $user1->setEmail("testChat1@sopinetchat.com");
        $user1->setEmailCanonical("testChat1@sopinetchat.com");
        $user1->setUsername("testChat1@sopinetchat.com");
        $user1->setUsernameCanonical("testChat1@sopinetchat.com");
        $user1->setPlainPassword("test");
        $user1->setEnabled(true);

        $userManager->updateUser($user1);

        /** @var User $user2 */
        $user2 = $userManager->createUser();

        $user2->setEmail("testChat2@sopinetchat.com");
        $user2->setEmailCanonical("testChat2@sopinetchat.com");
        $user2->setUsername("testChat2@sopinetchat.com");
        $user2->setUsernameCanonical("testChat2@sopinetchat.com");
        $user2->setPlainPassword("test");
        $user2->setEnabled(true);

        $userManager->updateUser($user2);

        // TODO: Do test unit with Login Abstract?

        /** @var InterfaceHelper $interfaceHelper */
        $interfaceHelper = $this->container->get('sopinet_chatbundle_interfacehelper');

        //$interfaceHelper->createChat()
    }
}