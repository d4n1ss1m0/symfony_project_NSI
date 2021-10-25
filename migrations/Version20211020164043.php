<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211020164043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE document_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE documents_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tags_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE document (id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE document_tags (document_id INT NOT NULL, tags_id INT NOT NULL, PRIMARY KEY(document_id, tags_id))');
        $this->addSql('CREATE INDEX IDX_C80818B5C33F7837 ON document_tags (document_id)');
        $this->addSql('CREATE INDEX IDX_C80818B58D7B4FB4 ON document_tags (tags_id)');
        $this->addSql('CREATE TABLE documents (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tags (id INT NOT NULL, name VARCHAR(50) NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE document_tags ADD CONSTRAINT FK_C80818B5C33F7837 FOREIGN KEY (document_id) REFERENCES document (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE document_tags ADD CONSTRAINT FK_C80818B58D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE document_tags DROP CONSTRAINT FK_C80818B5C33F7837');
        $this->addSql('ALTER TABLE document_tags DROP CONSTRAINT FK_C80818B58D7B4FB4');
        $this->addSql('DROP SEQUENCE document_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE documents_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tags_id_seq CASCADE');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE document_tags');
        $this->addSql('DROP TABLE documents');
        $this->addSql('DROP TABLE tags');
    }
}
