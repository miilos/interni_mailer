<?php

namespace App\Repository;

use App\Entity\WebauthnCredentialSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Webauthn\Bundle\Repository\DoctrineCredentialSourceRepository;
use Webauthn\PublicKeyCredentialSource;

/**
 * @extends ServiceEntityRepository<WebauthnCredentialSource>
 */
class WebauthnCredentialSourceRepository extends DoctrineCredentialSourceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebauthnCredentialSource::class);
    }

    public function saveCredentialSource(PublicKeyCredentialSource $publicKeyCredentialSource): void
    {
        if (!$publicKeyCredentialSource instanceof WebauthnCredentialSource) {
            $publicKeyCredentialSource = new WebauthnCredentialSource(
                $publicKeyCredentialSource->publicKeyCredentialId,
                $publicKeyCredentialSource->type,
                $publicKeyCredentialSource->transports,
                $publicKeyCredentialSource->attestationType,
                $publicKeyCredentialSource->trustPath,
                $publicKeyCredentialSource->aaguid,
                $publicKeyCredentialSource->credentialPublicKey,
                $publicKeyCredentialSource->userHandle,
                $publicKeyCredentialSource->counter
            );

            parent::saveCredentialSource($publicKeyCredentialSource);
        }
    }
}
