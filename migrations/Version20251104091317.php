<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251104091317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE search_query (id UUID NOT NULL, users UUID DEFAULT NULL, query VARCHAR(255) NOT NULL, lead_type VARCHAR(50) NOT NULL, max_results INT DEFAULT 25 NOT NULL, parameters JSON DEFAULT NULL, status VARCHAR(50) NOT NULL, results_count INT DEFAULT 0 NOT NULL, error VARCHAR(1024) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_108876021483A5E9 ON search_query (users)');
        $this->addSql('CREATE INDEX IDX_108876027B00651C ON search_query (status)');
        $this->addSql('CREATE INDEX IDX_108876028B8E8428 ON search_query (created_at)');
        $this->addSql('COMMENT ON COLUMN search_query.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN search_query.users IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN search_query.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN search_query.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE search_query ADD CONSTRAINT FK_108876021483A5E9 FOREIGN KEY (users) REFERENCES "users" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE search_query DROP CONSTRAINT FK_108876021483A5E9');
        $this->addSql('DROP TABLE search_query');
    }
}
