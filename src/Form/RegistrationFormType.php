<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => ['placeholder' => 'Enter your name'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Enter your email'],

            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Password',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Enter your password'],
                'constraints' => [new NotBlank(['message' => 'Please enter a password',]), new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters', // max length allowed by Symfony for security reasons 
                    'max' => 4096,
                ]),],
            ])
            ->add('birthday', DateType::class, [
                'label' => 'Birthday',
                'widget' => 'single_text',
            ])
            ->add('country', CountryType::class, [
                'label' => 'Country',
                'attr' => ['placeholder' => 'Enter your country'],
            ])
            ->add('number', TextType::class, [
                'label' => 'Number',
                'attr' => ['placeholder' => 'Enter your number'],
            ])
            //TODO: no referral option
            ->add('referredBy', TextType::class, [
                'label' => 'Referred By',
                'attr' => ['placeholder' => 'Enter your referral'],
                'required' => false
            ])
            ->add('iGeniusUserID', TextType::class, [
                'label' => 'iGenius User ID',
                'attr' => ['placeholder' => 'Enter your iGenius User ID'],
                'required' => false
            ])
            ->add('nameOfEnroller', TextType::class, [
                'label' => 'Name of Enroller',
                'attr' => ['placeholder' => 'Enter your name of enroller'],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
