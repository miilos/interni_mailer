<?php

namespace App\Repository;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Webauthn\Bundle\Repository\CanGenerateUserEntity;
use Webauthn\Bundle\Repository\CanRegisterUserEntity;
use Webauthn\Bundle\Repository\PublicKeyCredentialUserEntityRepositoryInterface;
use Webauthn\PublicKeyCredentialUserEntity;

class WebAuthnCredentialUserRepository implements PublicKeyCredentialUserEntityRepositoryInterface, CanRegisterUserEntity, CanGenerateUserEntity
{
    public function __construct(
        private UserRepository $userRepository,
        private RequestStack $requestStack,
        private WebauthnCredentialSourceRepository $webauthnCredentialSourceRepository
    ) {}

    public function findOneByUsername(string $username): ?PublicKeyCredentialUserEntity
    {
        $request = $this->requestStack->getCurrentRequest();
        $isAttestationFlow = $request && str_contains($request->getPathInfo(), 'attestation');

        $user = $this->userRepository->findOneBy(['email' => $username]);

        if ($user) {
            if ($isAttestationFlow) {
                $existingCredentials = $this->webauthnCredentialSourceRepository->findAllForUserEntity(
                    PublicKeyCredentialUserEntity::create($username, (string) $user->getId(), $user->getFirstname()),
                );

                if (!$existingCredentials) {
                    return null;
                }
            }

            return $this->getUserEntity($user);
        }

        return null;
    }

    public function findOneByUserHandle(string $userHandle): ?PublicKeyCredentialUserEntity
    {
        $user = $this->userRepository->findOneBy(['id' => $userHandle]);
        return $this->getUserEntity($user);
    }

    private function getUserEntity(null|User $user): ?PublicKeyCredentialUserEntity
    {
        if ($user === null) {
            return null;
        }

        return new PublicKeyCredentialUserEntity(
            $user->getEmail(),
            (string) $user->getId(),
            $user->getFirstname(),
            null
        );
    }

    public function generateUserEntity(?string $username, ?string $displayName): PublicKeyCredentialUserEntity
    {
        $existingUser = $this->userRepository->findOneBy(['email' => $username]);

        if ($existingUser) {
            return PublicKeyCredentialUserEntity::create(
                $existingUser->getEmail(),
                (string) $existingUser->getId(),
                $existingUser->getFirstname() ?? $displayName,
                null
            );
        }

        return PublicKeyCredentialUserEntity::create(
            $username,
            bin2hex(random_bytes(32)),
            $displayName ?? $username,
            null
        );
    }

    // the admin creates the user accounts, the bundle should not be allowed to create new users
    public function saveUserEntity(PublicKeyCredentialUserEntity $userEntity): void
    {
        $existingUser = $this->userRepository->findOneBy(['email' => $userEntity->name]);

        if ($existingUser) {
            return;
        }
    }
}
