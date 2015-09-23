<?php

namespace Sopinet\ChatBundle\Form;

use AppBundle\Entity\Answer;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sopinet\ChatBundle\Entity\Message;
use Sopinet\ChatBundle\Entity\MessageImage;
use Sopinet\ChatBundle\Form\DataTransformer\FileTransformer;
use Sopinet\ChatBundle\Form\MessageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MessageImageType extends MessageType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('file', 'file')
        ;

        $builder->get('file')
            ->addModelTransformer(new FileTransformer($this->container, $this->request->files->get('file')));

        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
            /** @var MessageImage $message */
            $message = $event->getData();
            if (!$message) {
                return;
            }
            $message->setText($message->getFile()->getHttpWebPath($this->request));
            $event->setData($message);
        });
        //return $this->getFile()->getHttpWebPath($container, $request);

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sopinet\ChatBundle\Entity\MessageImage',
            'csrf_protection'   => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sopinet_chatbundle_messageimage';
    }
}