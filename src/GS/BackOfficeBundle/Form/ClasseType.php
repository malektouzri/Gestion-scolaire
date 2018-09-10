<?php
/**
 * Created by PhpStorm.
 * User: ACER
 * Date: 11/07/2018
 * Time: 11:38
 */

namespace GS\BackOfficeBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class ClasseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fichier',FileType::class,array('required'=>false,'attr'=>array('class'=>'form-control')));

    }

}