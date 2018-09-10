<?php
/**
 * Created by PhpStorm.
 * User: ACER
 * Date: 05/07/2018
 * Time: 16:32
 */

namespace GS\BackOfficeBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class DocumentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fichier',FileType::class,array('required'=>true,'attr'=>array('class'=>'form-control')))
        ->add('type',ChoiceType::class,array('required'=>true,'choices'=>array('Cours'=>'COURS','Exercice'=>'EXERCICE'),'choices_as_values'=>true,'multiple'=>false,'expanded'=>true,'attr'=>array('class'=>'form-control')));

            }
}