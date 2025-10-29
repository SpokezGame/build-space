<?php

namespace App\Form;

use App\Entity\Tutorial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TutorialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'label' => '<br> Description',
                'label_html' => true,
                'attr' => [
                'rows' => 4,
                'cols' => 10,
                'style' => 'width:100%;'
                ]
            ])
            ->add('library', null, ['disabled' => true])
            ->add('imageBuild', ImageType::class, [
                'label' => '<br> Screen of the complete build',
                'label_html' => true,
                'required' => false
            ])
            ->add('steps', FileType::class, [
                'label' => "<br> Steps of the tutorial <br>",
                'mapped' => false,
                'label_html' => true,
                'multiple' => true,      
                'required' => false,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tutorial::class,
        ]);
    }
}
