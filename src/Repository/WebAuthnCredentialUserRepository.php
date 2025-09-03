<?php

namespace App\Repository;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Webauthn\Bundle\Repository\PublicKeyCredentialUserEntityRepositoryInterface;
use Webauthn\PublicKeyCredentialUserEntity;

class WebAuthnCredentialUserRepository implements
    PublicKeyCredentialUserEntityRepositoryInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private RequestStack $requestStack,
    ) {}

    public function findOneByUsername(string $username): ?PublicKeyCredentialUserEntity
    {
        $user = $this->userRepository->findOneBy(['email' => $username]);
        return $this->getUserEntity($user);
    }

    public function findOneByUserHandle(string $userHandle): ?PublicKeyCredentialUserEntity
    {
        $session = $this->requestStack->getSession();
        $id = $session->get('new_user_id');

        $user = $this->userRepository->findOneBy(['id' => $id]);

        return $this->getUserEntity($user);
    }

    private function getUserEntity(?User $user): ?PublicKeyCredentialUserEntity
    {
        if (!$user) {
            return null;
        }

        return new PublicKeyCredentialUserEntity(
            $user->getUserIdentifier(),
            (string) $user->getId(),
            $user->getEmail(),
            null
        );
    }
}
