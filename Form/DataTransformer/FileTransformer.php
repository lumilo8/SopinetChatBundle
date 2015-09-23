<?php
// src/AppBundle/Form/DataTransformer/IssueToNumberTransformer.php
namespace Sopinet\ChatBundle\Form\DataTransformer;

use Sopinet\ChatBundle\Entity\Chat;
use Doctrine\Common\Persistence\EntityManager;
use Sopinet\ChatBundle\Service\FileHelper;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transform from timestamp to datetime
 * Reverse transform from datetime to timestamp
 *
 * Class TimeTransformer
 * @package Sopinet\ChatBundle\Form\DataTransformer
 */
class FileTransformer implements DataTransformerInterface
{
    private $container;
    private $em;
    private $requestFile;

    public function __construct(Container $container, $requestFile)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
        $this->requestFile = $requestFile;
    }

    /**
     * Transforms an file to a URL.
     *
     * @param  File|null $file
     * @return string (url)
     */
    public function transform($file)
    {
        if (null === $file) {
            return '';
        }

        return $file->getHttpWebPath($this->container);
    }


    public function reverseTransform($fileName)
    {
        $requestFile = $this->requestFile;
        // no timestamp? It's optional, so that's ok

        if ($requestFile == null) {
            return;
        }

        /** @var FileHelper $fileHelper */
        $fileHelper = $this->container->get('sopinet_chatbundle_filehelper');
        $sopinetFile = $fileHelper->uploadFileByFile($requestFile, 'file', '\Sopinet\ChatBundle\Entity\ImageFile');

        $this->em->persist($sopinetFile);
        $this->em->flush();

        return $sopinetFile;
    }
}