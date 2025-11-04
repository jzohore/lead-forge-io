<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251030113438 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE report_bug (id UUID NOT NULL, "user" UUID DEFAULT NULL, message TEXT NOT NULL, filename VARCHAR(255) NOT NULL, mime_type VARCHAR(50) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C577C7938D93D649 ON report_bug ("user")');
        $this->addSql('COMMENT ON COLUMN report_bug.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN report_bug."user" IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN report_bug.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN report_bug.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE report_bug ADD CONSTRAINT FK_C577C7938D93D649 FOREIGN KEY ("user") REFERENCES "users" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE report_bug DROP CONSTRAINT FK_C577C7938D93D649');
        $this->addSql('DROP TABLE report_bug');
    }
}
