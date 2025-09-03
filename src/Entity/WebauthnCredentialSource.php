<?php

namespace App\Entity;

use App\Repository\WebauthnCredentialSourceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\TrustPath\TrustPath;

#[ORM\Entity(repositoryClass: WebauthnCredentialSourceRepository::class)]
class WebauthnCredentialSource extends PublicKeyCredentialSource
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?string $id = null;

    public function __construct(
        string $publicKeyCredentialId,
        string $type,
        array $transports,
        string $attestationType,
        TrustPath $trustPath,
        Uuid $aaguid,
        string $credentialPublicKey,
        string $userHandle,
        int $counter,
        ?array $otherUI = null,
        ?bool $backupEligible = null,
        ?bool $backupStatus = null,
        ?bool $uvInitialized = null
    )
    {
        $this->id = Ulid::generate();

        parent::__construct(
            $publicKeyCredentialId,
            $type,
            $transports,
            $attestationType,
            $trustPath,
            $aaguid,
            $credentialPublicKey,
            $userHandle,
            $counter,
            $otherUI,
            $backupEligible,
            $backupStatus,
            $uvInitialized
        );
    }

    public function getId(): ?string
    {
        return $this->id;
    }
}
