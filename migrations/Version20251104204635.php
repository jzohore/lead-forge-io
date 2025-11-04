<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251104204635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_ca');
        $this->addSql('DROP INDEX idx_sector_size');
        $this->addSql('DROP INDEX idx_status');
        $this->addSql('ALTER TABLE company ADD siret VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD geo_adresse VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD date_creation TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE company DROP industry');
        $this->addSql('ALTER TABLE company DROP size_level');
        $this->addSql('ALTER TABLE company DROP postal_code');
        $this->addSql('ALTER TABLE company DROP city');
        $this->addSql('ALTER TABLE company DROP region');
        $this->addSql('ALTER TABLE company DROP status');
        $this->addSql('ALTER TABLE company DROP dirigeant_nom');
        $this->addSql('ALTER TABLE company DROP dirigeant_prenom');
        $this->addSql('ALTER TABLE company DROP ca');
        $this->addSql('COMMENT ON COLUMN company.date_creation IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE company ADD industry VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD size_level VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD postal_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD region VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD dirigeant_nom VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD dirigeant_prenom VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD ca INT DEFAULT NULL');
        $this->addSql('ALTER TABLE company DROP siret');
        $this->addSql('ALTER TABLE company DROP geo_adresse');
        $this->addSql('ALTER TABLE company DROP date_creation');
        $this->addSql('CREATE INDEX idx_ca ON company (ca)');
        $this->addSql('CREATE INDEX idx_sector_size ON company (industry)');
        $this->addSql('CREATE INDEX idx_status ON company (status)');
    }
}
