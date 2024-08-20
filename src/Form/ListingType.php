<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Listing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;

class ListingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title')
        ->add('description')
        ->add('user', HiddenType::class, [
            'data' => $options['user_id'],
            'mapped' => false, // Do not map this field to the entity
        ])
        ->add('companyName', TextType::class, [
            'required' => false,
        ])
        ->add('photo', FileType::class, [
            'label' => 'Photo (JPEG, PNG file)',
            'mapped' => false, // Do not map this field to the entity
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '4000k',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, GIF).',
                ])
            ],
        ])
            ->add('add_listing', SubmitType::class, ['label' => 'submit']);
            // ->add('user_id', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Listing::class,
            'user_id' => null,
        ]);
    }
}
