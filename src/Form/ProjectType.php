<?php

namespace App\Form;

use App\Entity\Label;
use App\Entity\Project;
use App\Entity\User;
use App\Entity\Workspace;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'] ?? null;

        $builder
            ->add('name')
            ->add('description')
            ->add('workspace', EntityType::class, [
                'class' => Workspace::class,
                'choice_label' => 'name',
                'choices' => $user ? $this->getAvailableWorkspaces($user) : [],
            ])
            ->add('label', EntityType::class, [
                'class' => Label::class,
                'choice_label' => 'name',
            ])
        ;
    }

    private function getAvailableWorkspaces($user)
    {
        $workspaces = [];
        // Workspaces dont je suis owner
        foreach ($user->getWorkspaces() as $ws) {
            $workspaces[$ws->getId()] = $ws;
        }
        // Workspaces où je suis membre (role editor)
        foreach ($user->getMemberShips() as $membership) {
            if ($membership->getRole() === 'editor') {
                $project = $membership->getProject();
                if ($project && $project->getWorkspace()) {
                    $ws = $project->getWorkspace();
                    $workspaces[$ws->getId()] = $ws;
                }
            }
        }
        return array_values($workspaces);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'user' => null,
        ]);
    }
}
