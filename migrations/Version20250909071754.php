<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250909071754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE email_body_changelog ADD user_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email_body_changelog ADD CONSTRAINT FK_851BF58AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_851BF58AA76ED395 ON email_body_changelog (user_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE email_body_changelog DROP FOREIGN KEY FK_851BF58AA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_851BF58AA76ED395 ON email_body_changelog
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email_body_changelog DROP user_id
        SQL);
    }
}
