<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230310024557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participation_collecte (participation_id INT NOT NULL, collecte_id INT NOT NULL, INDEX IDX_B986C6AA6ACE3B73 (participation_id), INDEX IDX_B986C6AA710A9AC6 (collecte_id), PRIMARY KEY(participation_id, collecte_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE participation_collecte ADD CONSTRAINT FK_B986C6AA6ACE3B73 FOREIGN KEY (participation_id) REFERENCES participation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participation_collecte ADD CONSTRAINT FK_B986C6AA710A9AC6 FOREIGN KEY (collecte_id) REFERENCES collecte (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE achats DROP FOREIGN KEY FK_9920924E82EA2E54');
        $this->addSql('ALTER TABLE achats CHANGE date_achat date_achat DATETIME NOT NULL, CHANGE full_name full_name VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE address address VARCHAR(255) NOT NULL, CHANGE tel tel INT NOT NULL, CHANGE city city VARCHAR(255) NOT NULL, CHANGE zip_code zip_code INT NOT NULL, CHANGE validate validate INT NOT NULL');
        $this->addSql('ALTER TABLE achats ADD CONSTRAINT FK_9920924E82EA2E54 FOREIGN KEY (commande_id) REFERENCES commands (id)');
        $this->addSql('ALTER TABLE collecte DROP FOREIGN KEY FK_55AE4A3D80998B06');
        $this->addSql('ALTER TABLE collecte CHANGE date_deb date_deb DATETIME NOT NULL, CHANGE date_fin date_fin DATETIME NOT NULL');
        $this->addSql('ALTER TABLE collecte ADD CONSTRAINT FK_55AE4A3D80998B06 FOREIGN KEY (collecte_categorie_id) REFERENCES collecte_categorie (id)');
        $this->addSql('ALTER TABLE commands DROP FOREIGN KEY FK_9A3E132CA76ED395');
        $this->addSql('ALTER TABLE commands ADD CONSTRAINT FK_9A3E132CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commands_produit DROP FOREIGN KEY FK_C4A57CF347EFB');
        $this->addSql('ALTER TABLE commands_produit DROP FOREIGN KEY FK_C4A57C82EA2E54');
        $this->addSql('ALTER TABLE commands_produit ADD CONSTRAINT FK_C4A57CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE commands_produit ADD CONSTRAINT FK_C4A57C82EA2E54 FOREIGN KEY (commande_id) REFERENCES commands (id)');
        $this->addSql('ALTER TABLE don_history DROP FOREIGN KEY FK_9171FC29AB84EE2');
        $this->addSql('ALTER TABLE don_history DROP FOREIGN KEY FK_9171FC29D86650F');
        $this->addSql('ALTER TABLE don_history ADD CONSTRAINT FK_9171FC29AB84EE2 FOREIGN KEY (funds_id_id) REFERENCES fundrising (id)');
        $this->addSql('ALTER TABLE don_history ADD CONSTRAINT FK_9171FC29D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA4584665A');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA4584665A FOREIGN KEY (product_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE product_favoris DROP FOREIGN KEY FK_58EADF824584665A');
        $this->addSql('ALTER TABLE product_favoris DROP FOREIGN KEY FK_58EADF82A76ED395');
        $this->addSql('ALTER TABLE product_favoris ADD CONSTRAINT FK_58EADF824584665A FOREIGN KEY (product_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE product_favoris ADD CONSTRAINT FK_58EADF82A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2791FDB457');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2791FDB457 FOREIGN KEY (categorie_produit_id) REFERENCES categorie_produit (id)');
        $this->addSql('ALTER TABLE user_notification DROP FOREIGN KEY FK_3F980AC8A76ED395');
        $this->addSql('ALTER TABLE user_notification DROP FOREIGN KEY FK_3F980AC8EF1A9D84');
        $this->addSql('ALTER TABLE user_notification ADD CONSTRAINT FK_3F980AC8A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_notification ADD CONSTRAINT FK_3F980AC8EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participation_collecte DROP FOREIGN KEY FK_B986C6AA6ACE3B73');
        $this->addSql('ALTER TABLE participation_collecte DROP FOREIGN KEY FK_B986C6AA710A9AC6');
        $this->addSql('DROP TABLE participation_collecte');
        $this->addSql('ALTER TABLE achats DROP FOREIGN KEY FK_9920924E82EA2E54');
        $this->addSql('ALTER TABLE achats CHANGE date_achat date_achat DATETIME DEFAULT NULL, CHANGE full_name full_name VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE tel tel INT DEFAULT NULL, CHANGE city city VARCHAR(255) DEFAULT NULL, CHANGE zip_code zip_code INT DEFAULT NULL, CHANGE validate validate INT DEFAULT NULL');
        $this->addSql('ALTER TABLE achats ADD CONSTRAINT FK_9920924E82EA2E54 FOREIGN KEY (commande_id) REFERENCES commands (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE collecte DROP FOREIGN KEY FK_55AE4A3D80998B06');
        $this->addSql('ALTER TABLE collecte CHANGE date_deb date_deb DATE NOT NULL, CHANGE date_fin date_fin DATE NOT NULL');
        $this->addSql('ALTER TABLE collecte ADD CONSTRAINT FK_55AE4A3D80998B06 FOREIGN KEY (collecte_categorie_id) REFERENCES collecte_categorie (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commands DROP FOREIGN KEY FK_9A3E132CA76ED395');
        $this->addSql('ALTER TABLE commands ADD CONSTRAINT FK_9A3E132CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commands_produit DROP FOREIGN KEY FK_C4A57CF347EFB');
        $this->addSql('ALTER TABLE commands_produit DROP FOREIGN KEY FK_C4A57C82EA2E54');
        $this->addSql('ALTER TABLE commands_produit ADD CONSTRAINT FK_C4A57CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commands_produit ADD CONSTRAINT FK_C4A57C82EA2E54 FOREIGN KEY (commande_id) REFERENCES commands (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE don_history DROP FOREIGN KEY FK_9171FC29D86650F');
        $this->addSql('ALTER TABLE don_history DROP FOREIGN KEY FK_9171FC29AB84EE2');
        $this->addSql('ALTER TABLE don_history ADD CONSTRAINT FK_9171FC29D86650F FOREIGN KEY (user_id_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE don_history ADD CONSTRAINT FK_9171FC29AB84EE2 FOREIGN KEY (funds_id_id) REFERENCES fundrising (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA4584665A');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA4584665A FOREIGN KEY (product_id) REFERENCES produit (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_favoris DROP FOREIGN KEY FK_58EADF82A76ED395');
        $this->addSql('ALTER TABLE product_favoris DROP FOREIGN KEY FK_58EADF824584665A');
        $this->addSql('ALTER TABLE product_favoris ADD CONSTRAINT FK_58EADF82A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_favoris ADD CONSTRAINT FK_58EADF824584665A FOREIGN KEY (product_id) REFERENCES produit (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2791FDB457');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2791FDB457 FOREIGN KEY (categorie_produit_id) REFERENCES categorie_produit (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_notification DROP FOREIGN KEY FK_3F980AC8A76ED395');
        $this->addSql('ALTER TABLE user_notification DROP FOREIGN KEY FK_3F980AC8EF1A9D84');
        $this->addSql('ALTER TABLE user_notification ADD CONSTRAINT FK_3F980AC8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_notification ADD CONSTRAINT FK_3F980AC8EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
