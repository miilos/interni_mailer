<?php

namespace App\Dto;

use App\Entity\User;
use App\Validator\UniqueName\UniqueName;
use Symfony\Component\Validator\Constraints as Assert;

class UserDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'You must enter a username!')]
        private ?string $userName = null,

        #[Assert\NotBlank(message: 'You must enter the user\'s first name!')]
        private ?string $firstName = null,

        #[Assert\NotBlank(message: 'You must enter the user\'s last name!')]
        private ?string $lastName = null,

        #[Assert\Email(message: 'You must enter a valid email address!')]
        #[Assert\NotBlank(message: 'You must enter the user\'s email!')]
        #[UniqueName(
            entityClass: User::class,
            repoMethod: 'getAllEmails',
            uniqueProperty: 'email',
            message: 'This email is already taken!'
        )]
        private ?string $email = null,

        #[Assert\NotBlank(message: 'You must select a role!')]
        private array $roles = []
    ) {}

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(?string $userName): void
    {
        $this->userName = $userName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }
}
