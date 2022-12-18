<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221218144727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion ADD document_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE5C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('CREATE INDEX IDX_8A66DCE5C33F7837 ON occasion (document_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE5C33F7837');
        $this->addSql('DROP INDEX IDX_8A66DCE5C33F7837 ON occasion');
        $this->addSql('ALTER TABLE occasion DROP document_id');
    }
}
