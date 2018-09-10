<?php
/**
 * Created by PhpStorm.
 * User: ACER
 * Date: 29/07/2018
 * Time: 14:47
 */

namespace GS\BackOfficeBundle\Form;


use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;

class AbsenceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('heure',TimeType::class,array('placeholder' => array('hour' => 'Heure', 'minute' => 'Minute'),
                'required'=>false,'attr'=>array('class'=>'form-control')))
        ->add('date',DateType::class,array('placeholder' => array('day' => 'Jour', 'month' => 'Mois','year'=>'Année'),
        'required'=>false,'attr'=>array('class'=>'form-control')));
        // ->add('type',ChoiceType::class,array('required'=>true,'choices'=>array('Orale'=>'Orale','Ecrit'=>'Ecrit','Autre'=>'Autre'),'choices_as_values'=>true,'multiple'=>false,'expanded'=>true,'attr'=>array('class'=>'form-control')));

    }
}