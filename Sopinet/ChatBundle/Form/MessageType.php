<?php

namespace Sopinet\ChatBundle\Form;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sopinet\ChatBundle\Entity\Message;
use Sopinet\ChatBundle\Form\DataTransformer\ChatTransformer;
use Sopinet\ChatBundle\Form\DataTransformer\TimeTransformer;
use Sopinet\ChatBundle\Form\DataTransformer\DeviceTransformer;
use Sopinet\ChatBundle\Form\DataTransformer\LoginTransformer;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToTimestampTransformer;

class MessageType extends AbstractType
{
    protected $container;
    protected $request;

    public function __construct(Container $container, Request $request)
    {
        $this->container = $container;
        $this->request = $request;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', 'text', array(
                'required' => true
            ))
            ->add('fromUser')
            ->add('fromDevice') // Device ID, it will transform to a Device Object
            ->add('chat') // Chat ID, it will transform to a Chat Object
            ->add('id') // Message ID unique
            ->add('fromTime', 'text') // TimeStamp, it will transform to DateTime
        ;

        $builder->get('fromDevice')
            ->addModelTransformer(new DeviceTransformer($this->container));

        $builder->get('chat')
            ->addModelTransformer(new ChatTransformer($this->container));

        $builder->get('fromTime')
            ->addModelTransformer(new DateTimeToTimestampTransformer());

        $builder->get('fromUser')
            ->addModelTransformer(new LoginTransformer($this->container, $this->request));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sopinet\ChatBundle\Entity\Message',
            'csrf_protection'   => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sopinet_chatbundle_message';
    }
}