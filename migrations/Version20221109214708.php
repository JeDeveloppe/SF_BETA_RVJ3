<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221109214708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adresse (id INT AUTO_INCREMENT NOT NULL, ville_id INT NOT NULL, user_id INT NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, is_facturation TINYINT(1) DEFAULT NULL, organisation VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, INDEX IDX_C35F0816A73F0036 (ville_id), INDEX IDX_C35F0816A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE boite (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(919) NOT NULL, editeur VARCHAR(30) DEFAULT NULL, annee VARCHAR(20) DEFAULT NULL, imageBlob LONGBLOB NOT NULL, slug VARCHAR(255) DEFAULT NULL, is_livrable TINYINT(1) NOT NULL, is_complet TINYINT(1) NOT NULL, poid_boite VARCHAR(255) DEFAULT NULL, age VARCHAR(10) DEFAULT NULL, nbr_joueurs VARCHAR(4) DEFAULT NULL, prix_ht VARCHAR(4) DEFAULT NULL, is_deee TINYINT(1) DEFAULT NULL, contenu TEXT DEFAULT NULL, message VARCHAR(255) DEFAULT NULL, is_on_line TINYINT(1) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', creator VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE configuration (id INT AUTO_INCREMENT NOT NULL, devis_delay_before_delete DOUBLE PRECISION NOT NULL, prefixe_facture VARCHAR(5) NOT NULL, prefixe_devis VARCHAR(5) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE departement (id INT AUTO_INCREMENT NOT NULL, pays_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_C1765B63A6E44244 (pays_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, envoi_id INT NOT NULL, paiement_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', total_ttc DOUBLE PRECISION NOT NULL, total_ht DOUBLE PRECISION NOT NULL, total_livraison DOUBLE PRECISION NOT NULL, numero_devis INT NOT NULL, numero_facture INT DEFAULT NULL, adresse_facturation LONGTEXT NOT NULL, adresse_livraison LONGTEXT NOT NULL, end_validation_devis DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_relance_devis TINYINT(1) NOT NULL, envoi_email_devis DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', taux_tva INT NOT NULL, is_delete_by_user TINYINT(1) DEFAULT NULL, INDEX IDX_D8698A76A76ED395 (user_id), INDEX IDX_D8698A763F97ECE5 (envoi_id), UNIQUE INDEX UNIQ_D8698A762A4C4478 (paiement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document_lignes (id INT AUTO_INCREMENT NOT NULL, boite_id INT DEFAULT NULL, occasion_id INT DEFAULT NULL, document_id INT NOT NULL, message LONGTEXT DEFAULT NULL, prix_vente DOUBLE PRECISION NOT NULL, reponse LONGTEXT DEFAULT NULL, INDEX IDX_D18A5C2C3C43472D (boite_id), INDEX IDX_D18A5C2C4034998F (occasion_id), INDEX IDX_D18A5C2CC33F7837 (document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE informations_legales (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, adresse_societe VARCHAR(255) NOT NULL, siret_societe INT NOT NULL, adresse_mail_site VARCHAR(255) NOT NULL, societe_webmaster VARCHAR(255) NOT NULL, nom_webmaster VARCHAR(255) NOT NULL, nom_societe VARCHAR(255) NOT NULL, site_url VARCHAR(255) NOT NULL, hebergeur_site VARCHAR(255) NOT NULL, taux_tva NUMERIC(3, 2) NOT NULL, INDEX IDX_2618D3A5F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE methode_envoi (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE occasion (id INT AUTO_INCREMENT NOT NULL, boite_id INT NOT NULL, reference VARCHAR(255) DEFAULT NULL, price_ht VARCHAR(10) NOT NULL, old_price_ht VARCHAR(210) NOT NULL, information LONGTEXT DEFAULT NULL, is_neuf TINYINT(1) NOT NULL, etat_boite VARCHAR(255) NOT NULL, etat_materiel VARCHAR(255) NOT NULL, regle_jeu VARCHAR(255) NOT NULL, is_on_line TINYINT(1) NOT NULL, is_donation TINYINT(1) NOT NULL, is_sale TINYINT(1) NOT NULL, donation DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', sale DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', means_of_sale VARCHAR(20) DEFAULT NULL, stock VARCHAR(1) NOT NULL, prix_de_vente VARCHAR(8) DEFAULT NULL, INDEX IDX_8A66DCE53C43472D (boite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paiement (id INT AUTO_INCREMENT NOT NULL, token_transaction VARCHAR(255) NOT NULL, time_transaction DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', moyen_paiement VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, boite_id INT DEFAULT NULL, occasion_id INT DEFAULT NULL, methode_envoi_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', message VARCHAR(255) DEFAULT NULL, image_blob LONGBLOB DEFAULT NULL, etat VARCHAR(255) NOT NULL, facturation LONGTEXT DEFAULT NULL, livraison LONGTEXT DEFAULT NULL, INDEX IDX_24CC0DF2A76ED395 (user_id), INDEX IDX_24CC0DF23C43472D (boite_id), INDEX IDX_24CC0DF24034998F (occasion_id), INDEX IDX_24CC0DF270612E52 (methode_envoi_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partenaire (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, ville_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, collecte LONGTEXT DEFAULT NULL, vend LONGTEXT DEFAULT NULL, is_don TINYINT(1) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, image_blob LONGBLOB NOT NULL, is_detachee TINYINT(1) DEFAULT NULL, is_complet TINYINT(1) DEFAULT NULL, is_ecommerce TINYINT(1) DEFAULT NULL, is_on_line TINYINT(1) DEFAULT NULL, INDEX IDX_32FFA373F92F3E70 (country_id), INDEX IDX_32FFA373A73F0036 (ville_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pays (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, iso_code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, department VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', nickname VARCHAR(255) DEFAULT NULL, level INT DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ville (id INT AUTO_INCREMENT NOT NULL, departement_id INT NOT NULL, ville_code_postal VARCHAR(15) NOT NULL, ville_nom VARCHAR(255) NOT NULL, ville_departement VARCHAR(255) NOT NULL, lng VARCHAR(255) NOT NULL, lat VARCHAR(255) NOT NULL, pays VARCHAR(5) NOT NULL, INDEX IDX_43C3D9C3CCF9E01E (departement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adresse ADD CONSTRAINT FK_C35F0816A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE adresse ADD CONSTRAINT FK_C35F0816A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE departement ADD CONSTRAINT FK_C1765B63A6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A763F97ECE5 FOREIGN KEY (envoi_id) REFERENCES methode_envoi (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A762A4C4478 FOREIGN KEY (paiement_id) REFERENCES paiement (id)');
        $this->addSql('ALTER TABLE document_lignes ADD CONSTRAINT FK_D18A5C2C3C43472D FOREIGN KEY (boite_id) REFERENCES boite (id)');
        $this->addSql('ALTER TABLE document_lignes ADD CONSTRAINT FK_D18A5C2C4034998F FOREIGN KEY (occasion_id) REFERENCES occasion (id)');
        $this->addSql('ALTER TABLE document_lignes ADD CONSTRAINT FK_D18A5C2CC33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE informations_legales ADD CONSTRAINT FK_2618D3A5F92F3E70 FOREIGN KEY (country_id) REFERENCES pays (id)');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE53C43472D FOREIGN KEY (boite_id) REFERENCES boite (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF23C43472D FOREIGN KEY (boite_id) REFERENCES boite (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF24034998F FOREIGN KEY (occasion_id) REFERENCES occasion (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF270612E52 FOREIGN KEY (methode_envoi_id) REFERENCES methode_envoi (id)');
        $this->addSql('ALTER TABLE partenaire ADD CONSTRAINT FK_32FFA373F92F3E70 FOREIGN KEY (country_id) REFERENCES pays (id)');
        $this->addSql('ALTER TABLE partenaire ADD CONSTRAINT FK_32FFA373A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F92F3E70 FOREIGN KEY (country_id) REFERENCES pays (id)');
        $this->addSql('ALTER TABLE ville ADD CONSTRAINT FK_43C3D9C3CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse DROP FOREIGN KEY FK_C35F0816A73F0036');
        $this->addSql('ALTER TABLE adresse DROP FOREIGN KEY FK_C35F0816A76ED395');
        $this->addSql('ALTER TABLE departement DROP FOREIGN KEY FK_C1765B63A6E44244');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76A76ED395');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A763F97ECE5');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A762A4C4478');
        $this->addSql('ALTER TABLE document_lignes DROP FOREIGN KEY FK_D18A5C2C3C43472D');
        $this->addSql('ALTER TABLE document_lignes DROP FOREIGN KEY FK_D18A5C2C4034998F');
        $this->addSql('ALTER TABLE document_lignes DROP FOREIGN KEY FK_D18A5C2CC33F7837');
        $this->addSql('ALTER TABLE informations_legales DROP FOREIGN KEY FK_2618D3A5F92F3E70');
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE53C43472D');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2A76ED395');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF23C43472D');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF24034998F');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF270612E52');
        $this->addSql('ALTER TABLE partenaire DROP FOREIGN KEY FK_32FFA373F92F3E70');
        $this->addSql('ALTER TABLE partenaire DROP FOREIGN KEY FK_32FFA373A73F0036');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F92F3E70');
        $this->addSql('ALTER TABLE ville DROP FOREIGN KEY FK_43C3D9C3CCF9E01E');
        $this->addSql('DROP TABLE adresse');
        $this->addSql('DROP TABLE boite');
        $this->addSql('DROP TABLE configuration');
        $this->addSql('DROP TABLE departement');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE document_lignes');
        $this->addSql('DROP TABLE informations_legales');
        $this->addSql('DROP TABLE methode_envoi');
        $this->addSql('DROP TABLE occasion');
        $this->addSql('DROP TABLE paiement');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE partenaire');
        $this->addSql('DROP TABLE pays');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE ville');
    }
}
