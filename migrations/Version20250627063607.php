<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250627063607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE email_log CHANGE email_id email_id VARCHAR(255) NOT NULL, CHANGE to_addr to_addr JSON NOT NULL, CHANGE cc cc JSON NOT NULL, CHANGE bcc bcc JSON NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE email_log CHANGE email_id email_id INT NOT NULL, CHANGE to_addr to_addr LONGTEXT NOT NULL COMMENT '(DC2Type:array)', CHANGE cc cc LONGTEXT NOT NULL COMMENT '(DC2Type:array)', CHANGE bcc bcc LONGTEXT NOT NULL COMMENT '(DC2Type:array)'
        SQL);
    }
}
