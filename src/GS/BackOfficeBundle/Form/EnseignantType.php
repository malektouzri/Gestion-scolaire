<?php

namespace GS\BackOfficeBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnseignantType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('nom',TextType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('prenom',TextType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('matricule',TextType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('specialite',ChoiceType::class,array('required'=>true,'choices'=>array('Langues'=>'Langues','Sciences'=>'Sciences','Sport'=>'Sport'),'multiple'=>false,'expanded'=>true,'choices_as_values'=>true,'attr'=>array('class'=>'form-control')))
            ->add('sexe',ChoiceType::class,array('required'=>true,'choices'=>array('Femme'=>'Femme','Homme'=>'Homme'),'choices_as_values'=>true,'multiple'=>false,'expanded'=>true,'attr'=>array('class'=>'form-control')))
            ->add('adresse',TextareaType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('email',EmailType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('telephone',NumberType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('photo',FileType::class,array('required'=>false,'attr'=>array('class'=>'form-control')))
            ->add('cin',TextType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
            ->add('fichier',FileType::class,array('required'=>false,'attr'=>array('class'=>'form-control')))


        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GS\BackOfficeBundle\Entity\Enseignant'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'gs_backofficebundle_enseignant';
    }


}
