<?php

namespace App\Form;

use App\Entity\Fundrising;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\File;
use  App\Controller\FundsController;

class FundrisingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
            ->add('TitreDon',TextType::class,['label'=>'Funds title',
            'attr'=>['placeholder'=>"Funds title"]])
            ->add('descriptionDon',TextType::class,['label'=>'Funds description',
            'attr'=>['placeholder'=>"Funds description"]])
            //->add('imageDon',FileType::class,['label'=>'img',
            //'attr'=>['placeholder'=>"img"], 'mapped' => false,])
            ->add('date_Don',DateType::class, ['label'=>'startDate',
            'attr'=>['placeholder'=>"Enter Start Date"]])
            ->add('date_don_limite',DateType::class, ['label'=>'endDate',
            'attr'=>['placeholder'=>"Enter end Date"]])
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Completed' => 0,
                    'In progress' => 1,
                    'Not completed' => 2,
                ]])
                ->add('objectif',TextType::class,['label'=>'objectif',
                'attr'=>['placeholder'=>"objectif"]])


              /*  ->add('imageDon', FileType::class, [
                    'label' => 'Votre image de fund (Image file uniquement)',
    
                    // unmapped means that this field is not associated to any entity property
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
                                'image/jpeg',
                                'image/jpg',
                                'image/gif',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid Image ',
                        ])
                    ],
                ])
              */

              ->add('imageDon', FileType::class, [
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
            
        
    
            //->add('submit', SubmitType::class, ['label' => 'Add New fundrising']);
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fundrising::class,
        ]);
    }
}
