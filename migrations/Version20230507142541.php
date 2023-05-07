<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230507142541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE boite_category (boite_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_97C0CDFB3C43472D (boite_id), INDEX IDX_97C0CDFB12469DE2 (category_id), PRIMARY KEY(boite_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE boite_category ADD CONSTRAINT FK_97C0CDFB3C43472D FOREIGN KEY (boite_id) REFERENCES boite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE boite_category ADD CONSTRAINT FK_97C0CDFB12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boite_category DROP FOREIGN KEY FK_97C0CDFB3C43472D');
        $this->addSql('ALTER TABLE boite_category DROP FOREIGN KEY FK_97C0CDFB12469DE2');
        $this->addSql('DROP TABLE boite_category');
    }
}
