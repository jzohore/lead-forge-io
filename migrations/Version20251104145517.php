<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251104145517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (id UUID NOT NULL, name VARCHAR(255) NOT NULL, siren VARCHAR(255) DEFAULT NULL, industry VARCHAR(255) DEFAULT NULL, size_level VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, region VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, dirigeant_nom VARCHAR(255) DEFAULT NULL, dirigeant_prenom VARCHAR(255) DEFAULT NULL, ca INT DEFAULT NULL, section_activite_principale VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_siren ON company (siren)');
        $this->addSql('CREATE INDEX idx_sector_size ON company (industry)');
        $this->addSql('CREATE INDEX idx_ca ON company (ca)');
        $this->addSql('CREATE INDEX idx_status ON company (status)');
        $this->addSql('COMMENT ON COLUMN company.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN company.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN company.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE company');
    }
}
