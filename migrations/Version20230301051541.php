<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230301051541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commandes (id INT AUTO_INCREMENT NOT NULL, produit_id_id INT DEFAULT NULL, user_id_id INT DEFAULT NULL, quantite INT NOT NULL, date_ajout DATE NOT NULL, check_out INT NOT NULL, INDEX IDX_35D4282C4FD8F9C3 (produit_id_id), INDEX IDX_35D4282C9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commandes ADD CONSTRAINT FK_35D4282C4FD8F9C3 FOREIGN KEY (produit_id_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE commandes ADD CONSTRAINT FK_35D4282C9D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE categorie_produit ADD order_categ INT DEFAULT NULL');
        $this->addSql('ALTER TABLE collecte DROP FOREIGN KEY FK_55AE4A3D80998B06');
        $this->addSql('ALTER TABLE collecte ADD CONSTRAINT FK_55AE4A3D80998B06 FOREIGN KEY (collecte_categorie_id) REFERENCES collecte_categorie (id)');
        $this->addSql('ALTER TABLE commands DROP FOREIGN KEY FK_9A3E132CA76ED395');
        $this->addSql('ALTER TABLE commands ADD CONSTRAINT FK_9A3E132CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commands_produit DROP FOREIGN KEY FK_C4A57C82EA2E54');
        $this->addSql('ALTER TABLE commands_produit DROP FOREIGN KEY FK_C4A57CF347EFB');
        $this->addSql('ALTER TABLE commands_produit ADD CONSTRAINT FK_C4A57C82EA2E54 FOREIGN KEY (commande_id) REFERENCES commands (id)');
        $this->addSql('ALTER TABLE commands_produit ADD CONSTRAINT FK_C4A57CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2791FDB457');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2791FDB457 FOREIGN KEY (categorie_produit_id) REFERENCES categorie_produit (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commandes DROP FOREIGN KEY FK_35D4282C4FD8F9C3');
        $this->addSql('ALTER TABLE commandes DROP FOREIGN KEY FK_35D4282C9D86650F');
        $this->addSql('DROP TABLE commandes');
        $this->addSql('ALTER TABLE categorie_produit DROP order_categ');
        $this->addSql('ALTER TABLE collecte DROP FOREIGN KEY FK_55AE4A3D80998B06');
        $this->addSql('ALTER TABLE collecte ADD CONSTRAINT FK_55AE4A3D80998B06 FOREIGN KEY (collecte_categorie_id) REFERENCES collecte_categorie (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commands DROP FOREIGN KEY FK_9A3E132CA76ED395');
        $this->addSql('ALTER TABLE commands ADD CONSTRAINT FK_9A3E132CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commands_produit DROP FOREIGN KEY FK_C4A57CF347EFB');
        $this->addSql('ALTER TABLE commands_produit DROP FOREIGN KEY FK_C4A57C82EA2E54');
        $this->addSql('ALTER TABLE commands_produit ADD CONSTRAINT FK_C4A57CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commands_produit ADD CONSTRAINT FK_C4A57C82EA2E54 FOREIGN KEY (commande_id) REFERENCES commands (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2791FDB457');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2791FDB457 FOREIGN KEY (categorie_produit_id) REFERENCES categorie_produit (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
