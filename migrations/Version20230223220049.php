<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223220049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE testt');
        $this->addSql('ALTER TABLE achats DROP FOREIGN KEY FK_9920924E82EA2E54');
        $this->addSql('ALTER TABLE achats ADD payment_method VARCHAR(255) DEFAULT NULL, ADD validate INT NOT NULL');
        $this->addSql('ALTER TABLE achats ADD CONSTRAINT FK_9920924E82EA2E54 FOREIGN KEY (commande_id) REFERENCES commands (id)');
        $this->addSql('ALTER TABLE commands DROP FOREIGN KEY FK_9A3E132CA76ED395');
        $this->addSql('ALTER TABLE commands ADD CONSTRAINT FK_9A3E132CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commands_produit DROP FOREIGN KEY FK_C4A57CF347EFB');
        $this->addSql('ALTER TABLE commands_produit DROP FOREIGN KEY FK_C4A57C82EA2E54');
        $this->addSql('ALTER TABLE commands_produit ADD CONSTRAINT FK_C4A57CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE commands_produit ADD CONSTRAINT FK_C4A57C82EA2E54 FOREIGN KEY (commande_id) REFERENCES commands (id)');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2791FDB457');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2791FDB457 FOREIGN KEY (categorie_produit_id) REFERENCES categorie_produit (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE testt (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE achats DROP FOREIGN KEY FK_9920924E82EA2E54');
        $this->addSql('ALTER TABLE achats DROP payment_method, DROP validate');
        $this->addSql('ALTER TABLE achats ADD CONSTRAINT FK_9920924E82EA2E54 FOREIGN KEY (commande_id) REFERENCES commands (id) ON UPDATE CASCADE ON DELETE CASCADE');
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
