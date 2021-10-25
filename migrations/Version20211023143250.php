<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211023143250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE document_tags (document_id INT NOT NULL, tags_id INT NOT NULL, PRIMARY KEY(document_id, tags_id))');
        $this->addSql('CREATE INDEX IDX_C80818B5C33F7837 ON document_tags (document_id)');
        $this->addSql('CREATE INDEX IDX_C80818B58D7B4FB4 ON document_tags (tags_id)');
        $this->addSql('ALTER TABLE document_tags ADD CONSTRAINT FK_C80818B5C33F7837 FOREIGN KEY (document_id) REFERENCES document (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE document_tags ADD CONSTRAINT FK_C80818B58D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE document_tags');
    }
}
