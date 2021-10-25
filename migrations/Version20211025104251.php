<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211025104251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document DROP CONSTRAINT fk_d8698a7612469de2');
        $this->addSql('DROP INDEX idx_d8698a7612469de2');
        $this->addSql('ALTER TABLE document DROP category_id');
        $this->addSql('ALTER TABLE document ALTER file_name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE document ALTER file_name DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN document.file_name IS NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE document ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE document ALTER file_name TYPE TEXT');
        $this->addSql('ALTER TABLE document ALTER file_name DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN document.file_name IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT fk_d8698a7612469de2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d8698a7612469de2 ON document (category_id)');
    }
}
