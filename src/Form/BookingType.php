<?php

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', TextType::class,
                $this->getConfiguration(
                    "Date d'arriver",
                    "La date à laquelle vous compter arriver"
                )
            )
            ->add('endDate', TextType::class,
                $this->getConfiguration(
                    "Date de départ",
                    "la date à laquelle vous quittez les lieux"
                )
            )
            ->add('comment', TextareaType::class,
                $this->getConfiguration(
                    false,
                    "Si vous avez une comentaire n'ezitez pas",
                    [
                        "required" => false
                    ]
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
