<?php

namespace App\Form;

use App\Entity\Theme;
use App\Repository\TutorialRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ThemeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Get the current tutorial from 'data' option passed to the form
        $theme = $options['data'] ?? null;
        // Get the theme's author
        $member = $theme->getMember();
        $builder
            ->add('name')
            ->add('member', null, ['disabled' => true])
            ->add('published', CheckboxType::class, ['required' => false])
            ->add('tutorials',
                null,
                // options :
                [
                    // 'by_reference' => false : save changes
                    'by_reference' => false,
                    // Permits multiple selection
                    'multiple' => true,
                    // Show in checkbox form
                    'expanded' => true,
                    // Adjust the loading of possible tutorials to those of the current member's library
                    // The use helps pass the member to the lambda
                    'query_builder' => function (TutorialRepository $er) use ($member) {
                    return $er->createQueryBuilder('o')
                    ->leftJoin('o.library', 'i')
                    ->leftJoin('i.member', 'm')
                    ->andWhere('m.id = :memberId')
                    ->setParameter('memberId', $member->getId());
                    }
                ]
                )
                ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Theme::class,
        ]);
    }
}
