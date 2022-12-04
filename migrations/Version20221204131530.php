<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221204131530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE configuration ADD grand_plateau_bois VARCHAR(50) NOT NULL, ADD grand_plateau_plastique VARCHAR(50) NOT NULL, ADD petit_plateau_bois VARCHAR(50) NOT NULL, ADD petit_plateau_plastique VARCHAR(50) NOT NULL, ADD piece_unique VARCHAR(50) NOT NULL, ADD piece_multiple VARCHAR(50) NOT NULL, ADD piece_metal_bois VARCHAR(50) NOT NULL, ADD autre_piece VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE configuration DROP grand_plateau_bois, DROP grand_plateau_plastique, DROP petit_plateau_bois, DROP petit_plateau_plastique, DROP piece_unique, DROP piece_multiple, DROP piece_metal_bois, DROP autre_piece');
    }
}
