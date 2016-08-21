<?php

namespace Sgpc\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('submit', 'submit', array(
                'label' => 'Crear listado',
                'attr' => array('class' => 'btn btn-sm btn-success')
            ));  
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sgpc\CoreBundle\Entity\Listing'
        ));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'listing';
    }
}
