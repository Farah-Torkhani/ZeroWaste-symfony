<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230221200440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achats ADD commande_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE achats ADD CONSTRAINT FK_9920924E462C4194 FOREIGN KEY (commande_id_id) REFERENCES commandes (id)');
        $this->addSql('CREATE INDEX IDX_9920924E462C4194 ON achats (commande_id_id)');
        $this->addSql('ALTER TABLE commandes ADD produit_id_id INT DEFAULT NULL, ADD user_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commandes ADD CONSTRAINT FK_35D4282C4FD8F9C3 FOREIGN KEY (produit_id_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE commandes ADD CONSTRAINT FK_35D4282C9D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_35D4282C4FD8F9C3 ON commandes (produit_id_id)');
        $this->addSql('CREATE INDEX IDX_35D4282C9D86650F ON commandes (user_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achats DROP FOREIGN KEY FK_9920924E462C4194');
        $this->addSql('DROP INDEX IDX_9920924E462C4194 ON achats');
        $this->addSql('ALTER TABLE achats DROP commande_id_id');
        $this->addSql('ALTER TABLE commandes DROP FOREIGN KEY FK_35D4282C4FD8F9C3');
        $this->addSql('ALTER TABLE commandes DROP FOREIGN KEY FK_35D4282C9D86650F');
        $this->addSql('DROP INDEX IDX_35D4282C4FD8F9C3 ON commandes');
        $this->addSql('DROP INDEX IDX_35D4282C9D86650F ON commandes');
        $this->addSql('ALTER TABLE commandes DROP produit_id_id, DROP user_id_id');
    }
}
