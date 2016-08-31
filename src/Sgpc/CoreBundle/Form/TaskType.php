<?php

namespace Sgpc\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TaskType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('priority', 'choice', array(
                    'choices'  => array(
                        3 => 'Baja',
                        2 => 'Media',
                        1 => 'Alta',
                    ),
                )
            )
            ->add('submit', 'submit', array(
                'label' => 'Crear tarea',
                'attr' => array('class' => 'btn btn-sm btn-success')
            ));        
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sgpc\CoreBundle\Entity\Task'
        ));
    }
}
