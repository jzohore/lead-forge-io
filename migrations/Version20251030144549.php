<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251030144549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report_bug DROP CONSTRAINT fk_c577c7938d93d649');
        $this->addSql('DROP INDEX idx_c577c7938d93d649');
        $this->addSql('ALTER TABLE report_bug RENAME COLUMN "user" TO users');
        $this->addSql('ALTER TABLE report_bug ADD CONSTRAINT FK_C577C7931483A5E9 FOREIGN KEY (users) REFERENCES "users" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_C577C7931483A5E9 ON report_bug (users)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE report_bug DROP CONSTRAINT FK_C577C7931483A5E9');
        $this->addSql('DROP INDEX IDX_C577C7931483A5E9');
        $this->addSql('ALTER TABLE report_bug RENAME COLUMN users TO "user"');
        $this->addSql('ALTER TABLE report_bug ADD CONSTRAINT fk_c577c7938d93d649 FOREIGN KEY ("user") REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_c577c7938d93d649 ON report_bug ("user")');
    }
}
