<?php

namespace GS\BackOfficeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EcoleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username',TextType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('password',TextType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('adresse',TextareaType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('email',EmailType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('telephone',NumberType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GS\BackOfficeBundle\Entity\Ecole'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'gs_backofficebundle_ecole';
    }


}
