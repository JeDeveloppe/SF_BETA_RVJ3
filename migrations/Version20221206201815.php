<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221206201815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE time_transaction time_transaction DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE moyen_paiement moyen_paiement VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement DROP created_at, CHANGE time_transaction time_transaction DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE moyen_paiement moyen_paiement VARCHAR(20) NOT NULL');
    }
}
