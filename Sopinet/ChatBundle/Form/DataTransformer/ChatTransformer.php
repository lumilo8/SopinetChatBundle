<?php
// src/AppBundle/Form/DataTransformer/IssueToNumberTransformer.php
namespace Sopinet\ChatBundle\Form\DataTransformer;

use Sopinet\ChatBundle\Entity\Chat;
use Doctrine\Common\Persistence\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ChatTransformer implements DataTransformerInterface
{
    private $container;
    private $em;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
    }

    /**
    * Transforms an object (chat) to a string (number).
    *
    * @param  Chat|null $chat
    * @return string
    */
    public function transform($chat)
    {
        if (null === $chat) {
            return '';
        }

        return $chat->getId();
    }

    /**
    * Transforms a string (number) to an object (chat).
    *
    * @param  string $chatID
    * @return Chat|null
    * @throws TransformationFailedException if object (chat) is not found.
    */
    public function reverseTransform($chatID)
    {
        // no chat number? It's optional, so that's ok
        if (!$chatID) {
            return;
        }

        $reChat = $this->em->getRepository('SopinetChatBundle:Chat');
        $chat = $reChat->find($chatID);

        if (null === $chat) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
            'An chat with id "%s" does not exist!',
                $chatID
            ));
        }

        return $chat;
    }
}