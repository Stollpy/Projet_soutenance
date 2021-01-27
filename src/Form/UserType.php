<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, ['label' => 'Prénom'])
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'constraints' =>[
                    new NotBlank()
                ]
            ])
            ->add('PasswordNotEncoder', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'first_options' =>['label' =>'Votre mot de passe'],
                'second_options' =>['label' =>'Comfirmez votre mot de passe'],
                'constraints' =>[
                    new NotBlank(),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au minimun {{ limit }} caractères !'
                    ])
                ]
            ])
            ->add('addressline1', TextType::class, [
                'label' => 'Numero et voie',
                 'constraints' => [
                     new NotBlank()
                 ]])
            ->add('addressline2', TextType::class, [
                'label' => 'Complement d\'addresse',
                'required' => false
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Code Postal',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 5,
                    ])
                ]])
            ->add('city', TextType::class, [
                'label' => 'Ville'
            ])
            ->add('phone', TelType::class, [
                'label' => 'Votre numéro de téléphone'
            ])
            ->add('city', TextType::class, ['label' => 'Ville'])
            ->add('phone', TelType::class, ['label' => 'Votre numéro de téléphone'])
            ->add('firstname', TextType::class, ['label' => 'Votre prénom'])
            ->add('lastname', TextType::class, ['label' => 'Votre nom']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
