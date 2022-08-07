<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220708095810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partenaire ADD ville_id INT DEFAULT NULL, CHANGE image_blob image_blob LONGBLOB NOT NULL');
        $this->addSql('ALTER TABLE partenaire ADD CONSTRAINT FK_32FFA373A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('CREATE INDEX IDX_32FFA373A73F0036 ON partenaire (ville_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse CHANGE last_name last_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE first_name first_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE adresse adresse VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE organisation organisation VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE boite CHANGE nom nom VARCHAR(919) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE editeur editeur VARCHAR(30) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE annee annee VARCHAR(20) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE slug slug VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE nbr_joueurs nbr_joueurs VARCHAR(2) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE contenu contenu TEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE message message VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE configuration CHANGE prefixe_facture prefixe_facture VARCHAR(5) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE prefixe_devis prefixe_devis VARCHAR(5) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE document CHANGE token token VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE adresse_facturation adresse_facturation LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE adresse_livraison adresse_livraison LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE document_lignes CHANGE message message LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE reponse reponse LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE informations_legales CHANGE adresse_societe adresse_societe VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE adresse_mail_site adresse_mail_site VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE societe_webmaster societe_webmaster VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE nom_webmaster nom_webmaster VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE nom_societe nom_societe VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE site_url site_url VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE hebergeur_site hebergeur_site VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE methode_envoi CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE occasion CHANGE reference reference VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE information information LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE etat_boite etat_boite VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE etat_materiel etat_materiel VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE regle_jeu regle_jeu VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE donation donation VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE sale sale VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE paiement CHANGE token_transaction token_transaction VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE moyen_paiement moyen_paiement VARCHAR(20) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE panier CHANGE message message VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE etat etat VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE facturation facturation LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE livraison livraison LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE partenaire DROP FOREIGN KEY FK_32FFA373A73F0036');
        $this->addSql('DROP INDEX IDX_32FFA373A73F0036 ON partenaire');
        $this->addSql('ALTER TABLE partenaire DROP ville_id, CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description description LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE collecte collecte LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE vend vend LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE url url VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE image_blob image_blob LONGBLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE pays CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE iso_code iso_code VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE phone phone VARCHAR(20) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE department department VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ville CHANGE ville_code_postal ville_code_postal VARCHAR(15) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE ville_nom ville_nom VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE ville_departement ville_departement VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE lng lng VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE lat lat VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE pays pays VARCHAR(5) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`');
    }
}