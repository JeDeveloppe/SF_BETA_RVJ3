<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221102204713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion CHANGE price_ht price_ht VARCHAR(10) NOT NULL, CHANGE old_price_ht old_price_ht VARCHAR(210) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion CHANGE price_ht price_ht NUMERIC(5, 2) NOT NULL, CHANGE old_price_ht old_price_ht NUMERIC(5, 2) DEFAULT NULL');
    }
}
