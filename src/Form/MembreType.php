<?php

namespace App\Form;

use App\Entity\Membre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MembreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'choices' => [
                    'ROLE_USER' => "ROLE_USER",
                    'ROLE_ADMIN' => "ROLE_ADMIN"
                ]
            ])
            ->add('password')
            ->add('pseudo')
            ->add('nom')
            ->add('prenom')
            ->add('civilite', ChoiceType::class, [
                'choices' => [
                'Homme' => 'homme',
                'Femme' => 'femme',
                'Autre' => 'autre'
                ]
            ])
            // ->add('date_enregistrement')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Membre::class,
        ]);
    }
}
