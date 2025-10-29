<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251027143341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE early_access (id UUID NOT NULL, email VARCHAR(180) NOT NULL, slug VARCHAR(255) NOT NULL, is_verified BOOLEAN DEFAULT false NOT NULL, validation_token VARCHAR(128) DEFAULT NULL, validation_token_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_name VARCHAR(100) DEFAULT NULL, user_segment VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C3BC2A88E7927C74 ON early_access (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C3BC2A88989D9B62 ON early_access (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C3BC2A88B724A428 ON early_access (validation_token)');
        $this->addSql('COMMENT ON COLUMN early_access.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN early_access.validation_token_expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN early_access.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN early_access.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE user_profil (id UUID NOT NULL, user_id UUID DEFAULT NULL, credits_remaining INT DEFAULT 100 NOT NULL, credits_total INT DEFAULT 100 NOT NULL, stripe_customer_id VARCHAR(100) DEFAULT NULL, subscription_ends_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, subscription_status VARCHAR(50) DEFAULT NULL, subscription_plan VARCHAR(50) DEFAULT NULL, phone VARCHAR(35) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8384A9AA708DC647 ON user_profil (stripe_customer_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8384A9AAA76ED395 ON user_profil (user_id)');
        $this->addSql('COMMENT ON COLUMN user_profil.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_profil.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_profil.subscription_ends_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_profil.phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN user_profil.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_profil.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_profil ADD CONSTRAINT FK_8384A9AAA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX uniq_8d93d649708dc647');
        $this->addSql('ALTER TABLE "user" DROP credits_remaining');
        $this->addSql('ALTER TABLE "user" DROP credits_total');
        $this->addSql('ALTER TABLE "user" DROP stripe_customer_id');
        $this->addSql('ALTER TABLE "user" DROP subscription_status');
        $this->addSql('ALTER TABLE "user" DROP subscription_plan');
        $this->addSql('ALTER TABLE "user" DROP subscription_ends_at');
        $this->addSql('ALTER TABLE "user" DROP phone');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_profil DROP CONSTRAINT FK_8384A9AAA76ED395');
        $this->addSql('DROP TABLE early_access');
        $this->addSql('DROP TABLE user_profil');
        $this->addSql('ALTER TABLE "user" ADD credits_remaining INT DEFAULT 100 NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD credits_total INT DEFAULT 100 NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD stripe_customer_id VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD subscription_status VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD subscription_plan VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD subscription_ends_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD phone VARCHAR(35) DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "user".subscription_ends_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649708dc647 ON "user" (stripe_customer_id)');
    }
}
