<?php

namespace App\Form;

use App\Entity\Note;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class NoteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 50,
                        'minMessage' => 'Les commentaires doivent contenir au moins 50 caractères.',
                        'max' => 500,
                        'maxMessage' => 'Votre commentaire ne peut dépasser les 500 caractères.'
                    ])
                ]
            ])
            ->add('value', HiddenType::class, [
//                'value' => '',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
