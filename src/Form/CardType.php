<?php

namespace App\Form;

use App\Entity\Board;
use App\Entity\Card;
use App\Entity\Label;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('position')
            ->add('due_at', null, [
                'widget' => 'single_text',
            ])
            ->add('board', EntityType::class, [
                'class' => Board::class,
                'choice_label' => 'name',
            ])
            ->add('label', EntityType::class, [
                'class' => Label::class,
                'choice_label' => 'name',
            ])
            ->add('board', EntityType::class, [
                'class' => Board::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Card::class,
        ]);
    }
}
