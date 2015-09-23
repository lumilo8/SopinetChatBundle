<?php
// src/AppBundle/Form/DataTransformer/IssueToNumberTransformer.php
namespace Sopinet\ChatBundle\Form\DataTransformer;

use Sopinet\ChatBundle\Entity\Chat;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DeviceTransformer implements DataTransformerInterface
{
    private $container;
    private $em;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
    }

    /**
     * Transforms an object (device) to a string (number).
     *
     * @param  Device|null $device
     * @return string
     */
    public function transform($device)
    {
        if (null === $device) {
            return '';
        }

        return $device->getId();
    }

    /**
     * Transforms a string (number) to an object (chat).
     *
     * @param  string $chatID
     * @return Chat|null
     * @throws TransformationFailedException if object (chat) is not found.
     */
    public function reverseTransform($deviceID)
    {
        // no chat number? It's optional, so that's ok
        if (!$deviceID) {
            return;
        }

        $reDevice = $this->em->getRepository('SopinetChatBundle:Device');
        $device = $reDevice->find($deviceID);

        if (null === $device) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An chat with id "%s" does not exist!',
                $deviceID
            ));
        }

        return $device;
    }
}