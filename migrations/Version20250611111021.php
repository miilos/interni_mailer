<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250611111021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE email_template (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, from_addr VARCHAR(255) NOT NULL, to_addr LONGTEXT NOT NULL COMMENT '(DC2Type:array)', cc LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', bcc LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', body LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email CHANGE to_addr to_addr JSON NOT NULL, CHANGE cc cc JSON NOT NULL, CHANGE bcc bcc JSON NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE email_template
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email CHANGE to_addr to_addr LONGTEXT NOT NULL COMMENT '(DC2Type:array)', CHANGE cc cc LONGTEXT NOT NULL COMMENT '(DC2Type:array)', CHANGE bcc bcc LONGTEXT NOT NULL COMMENT '(DC2Type:array)'
        SQL);
    }
}
