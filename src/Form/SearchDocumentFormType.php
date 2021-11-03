<?php

namespace App\Form;

use App\Entity\Document;
use App\Entity\Tags;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\All;

class SearchDocumentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('Text')
        ->add('Tags', EntityType::class, array(
            'class' => Tags::class,
            'multiple' => true,
            'expanded' => true,
            'label' => 'Введите тэги документа',
        ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
