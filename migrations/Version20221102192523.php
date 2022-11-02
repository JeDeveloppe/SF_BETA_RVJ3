<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221102192523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion ADD is_donation TINYINT(1) NOT NULL, ADD is_sale TINYINT(1) NOT NULL, ADD means_of_sale VARCHAR(20) DEFAULT NULL, CHANGE donation donation DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE sale sale DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion DROP is_donation, DROP is_sale, DROP means_of_sale, CHANGE donation donation TINYINT(1) NOT NULL, CHANGE sale sale TINYINT(1) NOT NULL');
    }
}
