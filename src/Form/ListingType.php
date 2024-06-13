<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Listing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
