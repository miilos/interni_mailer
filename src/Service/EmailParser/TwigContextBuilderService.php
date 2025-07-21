<?php

namespace App\Service\EmailParser;

use App\Entity\EmailVariable;
use App\Repository\EmailVariableRepository;
use App\Repository\UserRepository;

class TwigContextBuilderService
{
    private array $context;
    public function __construct(
        private EmailVariableRepository $emailVariableRepository,
        private UserRepository $userRepository,
    ) {
        $this->context['globals'] = $this->getGlobals();
        // $this->context['users'] = $this->userRepository->findAll();
    }

    public function getContext(): array
    {
        return $this->context;
    }

    private function getGlobals(): array
    {
        $varEntities = $this->emailVariableRepository->findAll();

        $globals = [];
        foreach ($varEntities as $var) {
            $globals[$var->getName()] = $var->getValue();
        }

        return $globals;
    }
}
