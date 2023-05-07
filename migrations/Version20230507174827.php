<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230507174827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD envelope_id INT NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E664706CB17 FOREIGN KEY (envelope_id) REFERENCES envelope (id)');
        $this->addSql('CREATE INDEX IDX_23A0E664706CB17 ON article (envelope_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E664706CB17');
        $this->addSql('DROP INDEX IDX_23A0E664706CB17 ON article');
        $this->addSql('ALTER TABLE article DROP envelope_id');
    }
}
