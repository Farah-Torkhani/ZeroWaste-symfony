<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223204600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE achats (id INT AUTO_INCREMENT NOT NULL, commande_id INT DEFAULT NULL, date_achat DATETIME NOT NULL, full_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, tel INT NOT NULL, city VARCHAR(255) NOT NULL, zip_code INT NOT NULL, INDEX IDX_9920924E82EA2E54 (commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie_produit (id INT AUTO_INCREMENT NOT NULL, nom_categorie VARCHAR(255) NOT NULL, image_categorie VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE collecte (id INT AUTO_INCREMENT NOT NULL, collecte_categorie_id INT DEFAULT NULL, nom_collecte VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, image_collect VARCHAR(255) NOT NULL, etat INT NOT NULL, adresse VARCHAR(255) NOT NULL, date_deb DATE NOT NULL, date_fin DATE NOT NULL, INDEX IDX_55AE4A3D80998B06 (collecte_categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE collecte_categorie (id INT AUTO_INCREMENT NOT NULL, nom_collect_cat VARCHAR(255) NOT NULL, point_categorie INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commandes (id INT AUTO_INCREMENT NOT NULL, produit_id_id INT DEFAULT NULL, user_id_id INT DEFAULT NULL, quantite_c INT NOT NULL, date_ajout DATE NOT NULL, check_out INT NOT NULL, INDEX IDX_35D4282C4FD8F9C3 (produit_id_id), INDEX IDX_35D4282C9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commands (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, status INT NOT NULL, INDEX IDX_9A3E132CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commands_produit (id INT AUTO_INCREMENT NOT NULL, produit_id INT DEFAULT NULL, commande_id INT DEFAULT NULL, quantite_c INT NOT NULL, INDEX IDX_C4A57CF347EFB (produit_id), INDEX IDX_C4A57C82EA2E54 (commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE don (id INT AUTO_INCREMENT NOT NULL, titre_don VARCHAR(255) NOT NULL, description_don VARCHAR(255) NOT NULL, image_don VARCHAR(255) NOT NULL, date_don DATE NOT NULL, date_don_limite DATE NOT NULL, etat INT NOT NULL, objectif DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, verification_p INT NOT NULL, email VARCHAR(255) NOT NULL, phone INT NOT NULL, quantite INT NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, categorie_produit_id INT DEFAULT NULL, nom_produit VARCHAR(255) NOT NULL, quantite INT NOT NULL, prix_produit DOUBLE PRECISION NOT NULL, image VARCHAR(255) DEFAULT NULL, description VARCHAR(255) NOT NULL, prix_point_produit INT DEFAULT NULL, INDEX IDX_29A5EC2791FDB457 (categorie_produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, tel VARCHAR(255) NOT NULL, token VARCHAR(180) DEFAULT NULL, is_verified TINYINT(1) DEFAULT 0 NOT NULL, state TINYINT(1) DEFAULT 1 NOT NULL, description VARCHAR(255) DEFAULT NULL, fb_link VARCHAR(180) DEFAULT NULL, twitter_link VARCHAR(180) DEFAULT NULL, insta_link VARCHAR(180) DEFAULT NULL, img_url VARCHAR(180) DEFAULT \'defaultPic.jpg\', point INT DEFAULT 0, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE achats ADD CONSTRAINT FK_9920924E82EA2E54 FOREIGN KEY (commande_id) REFERENCES commands (id)');
        $this->addSql('ALTER TABLE collecte ADD CONSTRAINT FK_55AE4A3D80998B06 FOREIGN KEY (collecte_categorie_id) REFERENCES collecte_categorie (id)');
        $this->addSql('ALTER TABLE commandes ADD CONSTRAINT FK_35D4282C4FD8F9C3 FOREIGN KEY (produit_id_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE commandes ADD CONSTRAINT FK_35D4282C9D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commands ADD CONSTRAINT FK_9A3E132CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commands_produit ADD CONSTRAINT FK_C4A57CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE commands_produit ADD CONSTRAINT FK_C4A57C82EA2E54 FOREIGN KEY (commande_id) REFERENCES commands (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2791FDB457 FOREIGN KEY (categorie_produit_id) REFERENCES categorie_produit (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achats DROP FOREIGN KEY FK_9920924E82EA2E54');
        $this->addSql('ALTER TABLE collecte DROP FOREIGN KEY FK_55AE4A3D80998B06');
        $this->addSql('ALTER TABLE commandes DROP FOREIGN KEY FK_35D4282C4FD8F9C3');
        $this->addSql('ALTER TABLE commandes DROP FOREIGN KEY FK_35D4282C9D86650F');
        $this->addSql('ALTER TABLE commands DROP FOREIGN KEY FK_9A3E132CA76ED395');
        $this->addSql('ALTER TABLE commands_produit DROP FOREIGN KEY FK_C4A57CF347EFB');
        $this->addSql('ALTER TABLE commands_produit DROP FOREIGN KEY FK_C4A57C82EA2E54');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2791FDB457');
        $this->addSql('DROP TABLE achats');
        $this->addSql('DROP TABLE categorie_produit');
        $this->addSql('DROP TABLE collecte');
        $this->addSql('DROP TABLE collecte_categorie');
        $this->addSql('DROP TABLE commandes');
        $this->addSql('DROP TABLE commands');
        $this->addSql('DROP TABLE commands_produit');
        $this->addSql('DROP TABLE don');
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
