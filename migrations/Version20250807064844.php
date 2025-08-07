<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250807064844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE email_body_changelog ADD diff JSON DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email_body_changelog ADD CONSTRAINT FK_851BF58A5DA0FB8 FOREIGN KEY (template_id) REFERENCES email_body (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_851BF58A5DA0FB8 ON email_body_changelog (template_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE email_body_changelog DROP FOREIGN KEY FK_851BF58A5DA0FB8
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_851BF58A5DA0FB8 ON email_body_changelog
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email_body_changelog DROP diff
        SQL);
    }
}
