<?php

namespace GS\BackOfficeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParentEleveType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('cin',NumberType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('prenom',TextType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('adresse',TextareaType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('sexe',ChoiceType::class,array('required'=>true,'choices'=>array('Femme'=>'Femme','Homme'=>'Homme'),'choices_as_values'=>true,'multiple'=>false,'expanded'=>true,'attr'=>array('class'=>'form-control')))
            ->add('email',EmailType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('telephone',NumberType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('photo',FileType::class,array('required'=>false,'attr'=>array('class'=>'form-control')))        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GS\BackOfficeBundle\Entity\ParentEleve'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'gs_backofficebundle_parenteleve';
    }


}
