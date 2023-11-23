<?php

namespace App\Form;

use App\Entity\SupportCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupportCaseFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
      //  dd($this->getUser());
        $builder
            ->add('description',TextareaType::class)
            ->add('user',HiddenType::class)
            ->add('summary')
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'open' => 'Open',
                    'InProgress' => "inprogress",
                    'Cancelled' => "cancel",
                ],
            ])
               
            ->add('Submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupportCase::class,
        ]);
    }
}
