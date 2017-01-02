<?php

namespace Sgpc\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Sgpc\CoreBundle\Form\TaskType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScrumTaskType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder
                ->add('name')
                ->add('description')
                ->add('priority', 'choice', array(
                    'label' => 'Prioridad',
                    'choices' => array(
                        3 => 'Baja',
                        2 => 'Media',
                        1 => 'Alta',
                    ),
                        )
                )
                ->add('hours') 
                ->add('submit', 'submit', array(
                    'label' => 'Crear tarea',
                    'attr' => array('class' => 'btn btn-sm btn-success')
        ));
        
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sgpc\CoreBundle\Entity\ScrumTask'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sgpc_corebundle_scrumtask';
    }


}
