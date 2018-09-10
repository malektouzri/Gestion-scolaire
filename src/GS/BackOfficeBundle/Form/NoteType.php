<?php
/**
 * Created by PhpStorm.
 * User: ACER
 * Date: 18/07/2018
 * Time: 11:16
 */

namespace GS\BackOfficeBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class NoteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fichier',FileType::class,array('required'=>false,'attr'=>array('class'=>'form-control')));
           // ->add('type',ChoiceType::class,array('required'=>true,'choices'=>array('Orale'=>'Orale','Ecrit'=>'Ecrit','Autre'=>'Autre'),'choices_as_values'=>true,'multiple'=>false,'expanded'=>true,'attr'=>array('class'=>'form-control')));

    }
}