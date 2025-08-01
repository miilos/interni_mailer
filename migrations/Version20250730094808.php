<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250730094808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE email_template DROP FOREIGN KEY FK_9C0600CA8EE5DF4B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9C0600CA8EE5DF4B ON email_template
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email_template DROP body_template
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
            ALTER TABLE email_template ADD body_template VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE email_template ADD CONSTRAINT FK_9C0600CA8EE5DF4B FOREIGN KEY (body_template) REFERENCES email_body (name) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9C0600CA8EE5DF4B ON email_template (body_template)
        SQL);
    }
}
