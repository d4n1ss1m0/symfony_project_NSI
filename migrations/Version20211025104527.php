<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211025104527 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP CONSTRAINT fk_64c19c1c33f7837');
        $this->addSql('DROP INDEX idx_64c19c1c33f7837');
        $this->addSql('ALTER TABLE category DROP document_id');
        $this->addSql('ALTER TABLE document DROP file_name');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE document ADD file_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE category ADD document_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT fk_64c19c1c33f7837 FOREIGN KEY (document_id) REFERENCES document (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_64c19c1c33f7837 ON category (document_id)');
    }
}
