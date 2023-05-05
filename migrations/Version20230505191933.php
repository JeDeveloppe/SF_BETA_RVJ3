<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230505191933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article_boite (article_id INT NOT NULL, boite_id INT NOT NULL, INDEX IDX_AF9FFE7294869C (article_id), INDEX IDX_AF9FFE3C43472D (boite_id), PRIMARY KEY(article_id, boite_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article_boite ADD CONSTRAINT FK_AF9FFE7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_boite ADD CONSTRAINT FK_AF9FFE3C43472D FOREIGN KEY (boite_id) REFERENCES boite (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article_boite DROP FOREIGN KEY FK_AF9FFE7294869C');
        $this->addSql('ALTER TABLE article_boite DROP FOREIGN KEY FK_AF9FFE3C43472D');
        $this->addSql('DROP TABLE article_boite');
    }
}
