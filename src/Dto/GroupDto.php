<?php

namespace App\Dto;

use App\Entity\Group;
use App\Validator\UniqueName\UniqueName;
use Symfony\Component\Validator\Constraints as Assert;

class GroupDto
{
    public function __construct(
        #[Assert\NotNull(message: 'Group name can\'t be empty!')]
        #[UniqueName(
            entityClass: Group::class,
            repoMethod: 'getAllGroupNames',
            message: 'This group name is already in use!'
        )]
        private string $name,

        #[Assert\NotNull(message: 'Group email address can\'t be empty!')]
        #[Assert\Email(message: 'Invalid group email address!')]
        private string $address,

        #[Assert\NotNull(message: 'Group must have recipients!')]
        private array $recipients
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }
}
