<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321202947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76AE4BD0E0');
        $this->addSql('DROP INDEX IDX_D8698A76AE4BD0E0 ON document');
        $this->addSql('ALTER TABLE document DROP etat_document_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document ADD etat_document_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76AE4BD0E0 FOREIGN KEY (etat_document_id) REFERENCES etat_document (id)');
        $this->addSql('CREATE INDEX IDX_D8698A76AE4BD0E0 ON document (etat_document_id)');
    }
}
