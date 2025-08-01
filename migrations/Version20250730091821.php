<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250730091821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_449E2B375E237E06 ON email_body (name)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email_template DROP FOREIGN KEY FK_9C0600CACC4E68B8
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9C0600CACC4E68B8 ON email_template
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email_template DROP body_template_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email_template ADD CONSTRAINT FK_9C0600CA583B6904 FOREIGN KEY (body_template_name) REFERENCES email_body (name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9C0600CA583B6904 ON email_template (body_template_name)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE email_template DROP FOREIGN KEY FK_9C0600CA583B6904
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9C0600CA583B6904 ON email_template
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email_template ADD body_template_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email_template ADD CONSTRAINT FK_9C0600CACC4E68B8 FOREIGN KEY (body_template_id) REFERENCES email_body (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9C0600CACC4E68B8 ON email_template (body_template_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_449E2B375E237E06 ON email_body
        SQL);
    }
}
