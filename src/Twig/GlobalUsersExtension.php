<?php

namespace App\Twig;

use App\Repository\UserRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class GlobalUsersExtension extends AbstractExtension implements GlobalsInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getGlobals(): array
    {
        return [
            'all_users' => $this->userRepository->findAll(),
        ];
    }
}
