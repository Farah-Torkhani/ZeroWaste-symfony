<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ProfilePic', FileType::class, [
                // unmapped means that this field is not associated to any entity property
                'attr' => [
                    'class' => 'prouct-add-form-image__file-btn',
                    'id' => 'update_user_ProfilePic',
                ],
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image',
                    ])
                ],
            ])
            ->add('fullName')
            // ->add('email')
            ->add('tel')
            ->add('description', TextareaType::class)
            ->add('fbLink')
            ->add('twitterLink')
            ->add('instaLink')
            // ->add('password', RepeatedType::class, [
            //     'type' => PasswordType::class,
            //     'first_options' => ['label' => 'Password'],
            //     'second_options' => ['label' => 'Confirm Password']
            // ])

            ->add('submit', SubmitType::class, [
                'label' => "Update",
                'attr' => [
                    'class' => 'navigation__primary-btn form__btn'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
