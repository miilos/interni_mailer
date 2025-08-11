<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250811062316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE email_batch (id INT AUTO_INCREMENT NOT NULL, batch_id VARCHAR(255) NOT NULL, email_id VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, from_addr VARCHAR(255) NOT NULL, to_addr JSON NOT NULL, cc JSON NOT NULL, bcc JSON NOT NULL, body LONGTEXT NOT NULL, status VARCHAR(255) NOT NULL, dispatched_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', body_template VARCHAR(255) DEFAULT NULL, email_template VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email_body_changelog CHANGE created_at created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE email_batch
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email_body_changelog CHANGE created_at created_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }
}
