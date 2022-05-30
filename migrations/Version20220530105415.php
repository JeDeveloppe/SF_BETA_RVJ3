<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220530105415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion ADD donation VARCHAR(255) DEFAULT NULL, ADD sale VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boite CHANGE nom nom VARCHAR(919) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE editeur editeur VARCHAR(30) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE annee annee VARCHAR(20) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE slug slug VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE nbr_joueurs nbr_joueurs VARCHAR(2) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE contenu contenu TEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE message message VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE informations_legales CHANGE adresse_societe adresse_societe VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE adresse_mail_site adresse_mail_site VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE societe_webmaster societe_webmaster VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE nom_webmaster nom_webmaster VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE nom_societe nom_societe VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE site_url site_url VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE hebergeur_site hebergeur_site VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE occasion DROP donation, DROP sale, CHANGE reference reference VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE information information LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE etat_boite etat_boite VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE etat_materiel etat_materiel VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE regle_jeu regle_jeu VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
