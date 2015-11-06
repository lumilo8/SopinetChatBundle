<?php

namespace Sopinet\ChatBundle\Listener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\DiscriminatorMap;

class DoctrineEventListener
{
    /**
     * @var array
     */
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        $metadata = $event->getClassMetadata();
        $class = $metadata->getReflectionClass();

        if ($class === null) {
            $class = new \ReflectionClass($metadata->getName());
        }

        /** @var DiscriminatorMap $discriminatorMap */
        $discriminatorMap = array();

        if ($class->getName() == "Sopinet\ChatBundle\Entity\Message") {
            // Basic Types
            foreach($this->config['basic_type_message'] as $keyType => $arrayType) {
                if ($arrayType['enabled']) {
                    $class = $arrayType['class'];
                    $discriminatorMap[$keyType] = $class;
                }
            }

            // Extra Types
            foreach($this->config['extra_type_message'] as $keyType => $arrayType) {
                if ($arrayType['enabled']) {
                    $class = $arrayType['class'];
                    $discriminatorMap[$keyType] = $class;
                }
            }

            // Add any message Type?
            if ($this->config['anyType']) {
                $discriminatorMap['any'] = "Sopinet\ChatBundle\Entity\MessageAny";
            }

            $metadata->setDiscriminatorMap($discriminatorMap);
        } else if ($class->getName() == "Sopinet\ChatBundle\Entity\Chat") {
            // Extra Chats
            foreach($this->config['extra_type_chat'] as $keyType => $arrayType) {
                if ($arrayType['enabled']) {
                    $class = $arrayType['class'];
                    $discriminatorMap[$keyType] = $class;
                }
            }

            $metadata->setDiscriminatorMap($discriminatorMap);
        }

        //$builder = new ClassMetadataBuilder($metadata);
        //$builder->create
        //$builder->createField("hola", "integer")->build();
        //$metadata->setParentClasses(array('\Sopinet\ChatBundle\Entity\MessageWithUser'));
    }
}