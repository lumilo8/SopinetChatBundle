<?php

namespace Sopinet\ChatBundle\Form;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sopinet\ChatBundle\Form\DataTransformer\ChatMembersTransformer;
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

class ChatType extends AbstractType
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
            ->add('name')
            ->add('admin')
            ->add('chatMembers', 'text') // ChatMembers String (ids in array) to ChatMembers Array Object
        ;

        $builder->get('admin')
            ->addModelTransformer(new LoginTransformer($this->container, $this->request));

        $builder->get('chatMembers')
            ->addModelTransformer(new ChatMembersTransformer($this->container));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sopinet\ChatBundle\Entity\Chat',
            'csrf_protection'   => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sopinet_chatbundle_chat';
    }
}