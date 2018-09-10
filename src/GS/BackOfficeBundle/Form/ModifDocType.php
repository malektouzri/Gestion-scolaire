<?php
/**
 * Created by PhpStorm.
 * User: ACER
 * Date: 09/07/2018
 * Time: 12:48
 */

namespace GS\BackOfficeBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class ModifDocType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fichier',FileType::class,array('required'=>true,'attr'=>array('class'=>'form-control')));

    }

}