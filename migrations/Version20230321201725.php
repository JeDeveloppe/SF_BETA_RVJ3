<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321201725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse ADD CONSTRAINT FK_C35F0816A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE adresse ADD CONSTRAINT FK_C35F0816A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66A490C0B6 FOREIGN KEY (boite_origine_id) REFERENCES boite (id)');
        $this->addSql('ALTER TABLE article_boite ADD CONSTRAINT FK_AF9FFE7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_boite ADD CONSTRAINT FK_AF9FFE3C43472D FOREIGN KEY (boite_id) REFERENCES boite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE departement ADD CONSTRAINT FK_C1765B63A6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A763F97ECE5 FOREIGN KEY (envoi_id) REFERENCES methode_envoi (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A762A4C4478 FOREIGN KEY (paiement_id) REFERENCES paiement (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76AE4BD0E0 FOREIGN KEY (etat_document_id) REFERENCES etat_document (id)');
        $this->addSql('ALTER TABLE document_lignes ADD CONSTRAINT FK_D18A5C2C3C43472D FOREIGN KEY (boite_id) REFERENCES boite (id)');
        $this->addSql('ALTER TABLE document_lignes ADD CONSTRAINT FK_D18A5C2C4034998F FOREIGN KEY (occasion_id) REFERENCES occasion (id)');
        $this->addSql('ALTER TABLE document_lignes ADD CONSTRAINT FK_D18A5C2CC33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE document_lignes ADD CONSTRAINT FK_D18A5C2C7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE informations_legales ADD CONSTRAINT FK_2618D3A5F92F3E70 FOREIGN KEY (country_id) REFERENCES pays (id)');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE53C43472D FOREIGN KEY (boite_id) REFERENCES boite (id)');
        $this->addSql('ALTER TABLE occasion ADD CONSTRAINT FK_8A66DCE5C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF23C43472D FOREIGN KEY (boite_id) REFERENCES boite (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF24034998F FOREIGN KEY (occasion_id) REFERENCES occasion (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF270612E52 FOREIGN KEY (methode_envoi_id) REFERENCES methode_envoi (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF27294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE panier_image ADD CONSTRAINT FK_EC102BE2F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id)');
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
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66A76ED395');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66A490C0B6');
        $this->addSql('ALTER TABLE article_boite DROP FOREIGN KEY FK_AF9FFE7294869C');
        $this->addSql('ALTER TABLE article_boite DROP FOREIGN KEY FK_AF9FFE3C43472D');
        $this->addSql('ALTER TABLE departement DROP FOREIGN KEY FK_C1765B63A6E44244');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76A76ED395');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A763F97ECE5');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A762A4C4478');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76AE4BD0E0');
        $this->addSql('ALTER TABLE document_lignes DROP FOREIGN KEY FK_D18A5C2C3C43472D');
        $this->addSql('ALTER TABLE document_lignes DROP FOREIGN KEY FK_D18A5C2C4034998F');
        $this->addSql('ALTER TABLE document_lignes DROP FOREIGN KEY FK_D18A5C2CC33F7837');
        $this->addSql('ALTER TABLE document_lignes DROP FOREIGN KEY FK_D18A5C2C7294869C');
        $this->addSql('ALTER TABLE informations_legales DROP FOREIGN KEY FK_2618D3A5F92F3E70');
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE53C43472D');
        $this->addSql('ALTER TABLE occasion DROP FOREIGN KEY FK_8A66DCE5C33F7837');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2A76ED395');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF23C43472D');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF24034998F');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF270612E52');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF27294869C');
        $this->addSql('ALTER TABLE panier_image DROP FOREIGN KEY FK_EC102BE2F77D927C');
        $this->addSql('ALTER TABLE partenaire DROP FOREIGN KEY FK_32FFA373F92F3E70');
        $this->addSql('ALTER TABLE partenaire DROP FOREIGN KEY FK_32FFA373A73F0036');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F92F3E70');
        $this->addSql('ALTER TABLE ville DROP FOREIGN KEY FK_43C3D9C3CCF9E01E');
    }
}
