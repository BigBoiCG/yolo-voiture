<?php

namespace App\Form;

use DateTime;
use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_heure_depart', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'min' => date_format(new \DateTime('+ 1 days'), "d M y")
                ],
            ])
            ->add('date_heure_fin', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'min' => date_format(new \DateTime('+ 2 days'), "d M y")
                ],
                'constraints' => [
                    new GreaterThanOrEqual(['propertyPath' => 'parent.all[date_heure_depart].data',
                    'message' => 'La date de fin doit être ultérieure à la date de réservation d\'au moins 1 jour']),],
            ])
            // ->add('prix_total')
            // ->add('date_enregistrement')
            // ->add('id_membre')
            // ->add('id_vehicule')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
