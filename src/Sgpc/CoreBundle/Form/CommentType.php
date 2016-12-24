<?php

namespace Sgpc\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\DomCrawler\Field\TextareaFormField;

class CommentType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder->add('comment', TextareaType::class, array(
            'label' => false,
            'attr' => array('placeholder' => 'Nuevo mensaje...'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Sgpc\CoreBundle\Entity\Comment'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'sgpc_corebundle_comment';
    }

    /**
     * @return string
     */
    public function getName() {
        return 'comment';
    }

}
