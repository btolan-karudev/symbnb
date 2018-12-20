<?php

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class,
                $this->getConfiguration(
                    "Date d'arriver",
                    "La date à laquelle vous compter arriver",
                    ["widget" => "single_text"]
                )
            )
            ->add('endDate', DateType::class,
                $this->getConfiguration(
                    "Date de départ",
                    "la date à laquelle vous quittez les lieux",
                    ["widget" => "single_text"]
                )
            )
            ->add('comment', TextareaType::class,
                $this->getConfiguration(
                    false,
                    "Si vous avez une comentaire n'ezitez pas"
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
