<?php

namespace App\Form;

use App\Dto\UserDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => ['class' => 'send-input'],
            ])
            ->add('firstname', TextType::class, [
                'attr' => ['class' => 'send-input'],
            ])
            ->add('lastname', TextType::class, [
                'attr' => ['class' => 'send-input'],
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'send-input'],
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'placeholder' => 'Select a role...',
                'attr' => ['class' => 'user-data-select'],
            ]);

        $builder->get('roles')->addModelTransformer(new CallbackTransformer(
            function (?array $roles) {
                return $roles[0] ?? null;
            },
            function (?string $role) {
                return $role ? [$role] : [];
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserDto::class,
            'csrf_protection' => false
        ]);
    }
}
