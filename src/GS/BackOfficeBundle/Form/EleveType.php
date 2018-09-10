<?php

namespace GS\BackOfficeBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EleveType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('prenom',TextType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('adresse',TextareaType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('sexe',ChoiceType::class,array('required'=>true,'choices'=>array('Femme'=>'Femme','Homme'=>'Homme'),'choices_as_values'=>true,'multiple'=>false,'expanded'=>true,'attr'=>array('class'=>'form-control')))
            ->add('numInscription',TextType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('photo',FileType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
           ->add('classe',HiddenType::class)
                 ;
    }
    //array('required'=>true,'class'=>'BackOfficeBundle:Classe','multiple'=>false,'choice_label'=>'nom','attr'=>array('class'=>'form-control'))
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GS\BackOfficeBundle\Entity\Eleve'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'gs_backofficebundle_eleve';
    }


}
