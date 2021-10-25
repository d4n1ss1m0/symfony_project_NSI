<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211025104101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD document_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1C33F7837 FOREIGN KEY (document_id) REFERENCES document (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_64C19C1C33F7837 ON category (document_id)');
        $this->addSql('ALTER TABLE document ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE document ADD file_name TEXT NOT NULL');
        $this->addSql('COMMENT ON COLUMN document.file_name IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D8698A7612469DE2 ON document (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE document DROP CONSTRAINT FK_D8698A7612469DE2');
        $this->addSql('DROP INDEX IDX_D8698A7612469DE2');
        $this->addSql('ALTER TABLE document DROP category_id');
        $this->addSql('ALTER TABLE document DROP file_name');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C1C33F7837');
        $this->addSql('DROP INDEX IDX_64C19C1C33F7837');
        $this->addSql('ALTER TABLE category DROP document_id');
    }
}
