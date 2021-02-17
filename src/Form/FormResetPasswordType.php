<?php

namespace App\Form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FormResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', RepeatedType::class,[
            'type' => PasswordType::class,
            'mapped' => false,
            'required' => true,
            'options' => array(
                'translation_domain' => 'security',
            ),
            'first_options' => array('label' => 'Parolă'),
            'second_options' => array('label' => 'Confirmă parola'),
            'invalid_message' => 'Parolele nu se potrivesc',
            'constraints' => [
                new NotBlank([
                    'message' => 'Parola nu poate fi goală',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Lungimea minimă este de 6 caractere',
                    'max' => 4096,
                ]),
            ],
        ]);
    }
}