<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230309201017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE don_history (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, funds_id_id INT DEFAULT NULL, comment VARCHAR(255) NOT NULL, donation_price DOUBLE PRECISION NOT NULL, date_donation DATE NOT NULL, INDEX IDX_9171FC29D86650F (user_id_id), INDEX IDX_9171FC29AB84EE2 (funds_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fundrising (id INT AUTO_INCREMENT NOT NULL, titre_don VARCHAR(255) NOT NULL, description_don VARCHAR(255) NOT NULL, image_don VARCHAR(255) NOT NULL, date_don DATE NOT NULL, date_don_limite DATE NOT NULL, etat VARCHAR(255) NOT NULL, objectif DOUBLE PRECISION NOT NULL, total DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE don_history ADD CONSTRAINT FK_9171FC29D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE don_history ADD CONSTRAINT FK_9171FC29AB84EE2 FOREIGN KEY (funds_id_id) REFERENCES fundrising (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE don_history DROP FOREIGN KEY FK_9171FC29D86650F');
        $this->addSql('ALTER TABLE don_history DROP FOREIGN KEY FK_9171FC29AB84EE2');
        $this->addSql('DROP TABLE don_history');
        $this->addSql('DROP TABLE fundrising');
    }
}
