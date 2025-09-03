<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250829083525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE webauthn_credential_source ADD public_key_credential_id LONGTEXT NOT NULL, ADD type VARCHAR(255) NOT NULL, ADD transports JSON NOT NULL, ADD attestation_type VARCHAR(255) NOT NULL, ADD trust_path JSON NOT NULL, ADD aaguid TINYTEXT NOT NULL, ADD credential_public_key LONGTEXT NOT NULL, ADD user_handle VARCHAR(255) NOT NULL, ADD counter INT NOT NULL, ADD other_ui JSON DEFAULT NULL, ADD backup_eligible TINYINT(1) DEFAULT NULL, ADD backup_status TINYINT(1) DEFAULT NULL, ADD uv_initialized TINYINT(1) DEFAULT NULL, CHANGE id id VARCHAR(255) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE webauthn_credential_source DROP public_key_credential_id, DROP type, DROP transports, DROP attestation_type, DROP trust_path, DROP aaguid, DROP credential_public_key, DROP user_handle, DROP counter, DROP other_ui, DROP backup_eligible, DROP backup_status, DROP uv_initialized, CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
    }
}
