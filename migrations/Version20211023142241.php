<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211023142241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE document_tags');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE document_tags (document_id INT NOT NULL, tags_id INT NOT NULL, PRIMARY KEY(document_id, tags_id))');
        $this->addSql('CREATE INDEX idx_c80818b5c33f7837 ON document_tags (document_id)');
        $this->addSql('CREATE INDEX idx_c80818b58d7b4fb4 ON document_tags (tags_id)');
        $this->addSql('ALTER TABLE document_tags ADD CONSTRAINT fk_c80818b5c33f7837 FOREIGN KEY (document_id) REFERENCES document (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE document_tags ADD CONSTRAINT fk_c80818b58d7b4fb4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
