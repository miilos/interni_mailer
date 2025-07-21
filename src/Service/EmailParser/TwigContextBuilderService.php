<?php

namespace App\Service\EmailParser;

use App\Dto\EmailDto;
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
        $this->buildContext();
    }

    // if the email dto is passed, the context includes only the user object of the user who is receiving the email
    // if the dto is not passed, the context includes all the users
    public function getContext(?EmailDto $emailDto): array
    {
        $context = $this->context;

        if ($emailDto) {
            foreach ($this->context['users'] as $user) {
                if ($user->getEmail() === $emailDto->getTo()) {
                    $context['user'] = $user;
                    unset($context['users']);
                }
            }
        }

        return $context;
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

    private function getUsers(): array
    {
        return $this->userRepository->findAll();
    }

    private function buildContext(): void
    {
        $this->context['globals'] = $this->getGlobals();
        $this->context['users'] = $this->getUsers();
    }
}
