<?php
/**
 * Created by PhpStorm.
 * User: ThinkCenter
 * Date: 12/12/2018
 * Time: 16:07
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType
{
    /**
     * Permet d avoir la configuration de base d'un champ
     *
     * @param $label
     * @param $placeholder
     * @param array $options
     * @return array
     */
    protected function getConfiguration($label, $placeholder, $options = [])
    {
        return array_merge_recursive([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]

        ],
            $options);
    }

}