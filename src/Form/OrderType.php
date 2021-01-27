<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class,  ['label' => 'Votre prénom', "attr" => ['readonly' => true]])
            ->add('lastname', TextType::class, ['label' => 'Votre nom', "attr" => ['readonly' => true]])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'constraints' =>[
                    new NotBlank()
                ],
                "attr" => ['readonly' => true]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Votre numéro de téléphone', "attr" => ['readonly' => true]
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
                'label' => 'Votre ville'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
