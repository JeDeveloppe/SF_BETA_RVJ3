<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230319173634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document_lignes ADD article_id INT DEFAULT NULL, ADD quantity INT DEFAULT NULL');
        $this->addSql('ALTER TABLE document_lignes ADD CONSTRAINT FK_D18A5C2C7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('CREATE INDEX IDX_D18A5C2C7294869C ON document_lignes (article_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document_lignes DROP FOREIGN KEY FK_D18A5C2C7294869C');
        $this->addSql('DROP INDEX IDX_D18A5C2C7294869C ON document_lignes');
        $this->addSql('ALTER TABLE document_lignes DROP article_id, DROP quantity');
    }
}
