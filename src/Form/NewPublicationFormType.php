<?php

namespace App\Form;

use App\Entity\Article;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewPublicationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('title', TextType::class,[
                'label' => 'Titre',
                'empty_data' => '',
                'constraints' =>[
                    new NotBlank([
                        'message' => 'Merci de renseigner un titre',
                    ]),
                    new Length([
                        'min' =>2,
                        'max' => 150,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le titre doit contenir au maximum {{ limit }} caractères',
                    ]),
                ],
            ])

            ->add('content', CKEditorType::class,[
                'label' => 'Contenu',
                'purify_html' =>true,
                'attr' =>[
                    'class' => 'd-none',
                ],
                'constraints' =>[
                    new NotBlank([
                        'message' => 'Merci de renseigner un contenu',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 50000,
                        'minMessage' => 'Le contenu doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le contenu doit contenir au maximum {{ limit }} caractères',
                    ]),
                ],
            ])

            ->add('save', SubmitType::class, [
                'label' => 'Publier',
                'attr' => [
                    'class' => 'btn btn-outline-primary w-100',
                ],
            ]);

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,


        ]);
    }
}
