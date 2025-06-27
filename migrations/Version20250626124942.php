<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250626124942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE email_log (id INT AUTO_INCREMENT NOT NULL, email_id INT NOT NULL, subject VARCHAR(255) NOT NULL, from_addr VARCHAR(255) NOT NULL, to_addr LONGTEXT NOT NULL COMMENT '(DC2Type:array)', cc LONGTEXT NOT NULL COMMENT '(DC2Type:array)', bcc LONGTEXT NOT NULL COMMENT '(DC2Type:array)', body LONGTEXT NOT NULL, status VARCHAR(255) NOT NULL, logged_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE email_log
        SQL);
    }
}
