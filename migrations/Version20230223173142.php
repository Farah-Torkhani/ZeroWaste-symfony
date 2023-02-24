<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223173142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE don_history ADD user_id_id INT DEFAULT NULL, ADD funds_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE don_history ADD CONSTRAINT FK_9171FC29D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE don_history ADD CONSTRAINT FK_9171FC29AB84EE2 FOREIGN KEY (funds_id_id) REFERENCES fundrising (id)');
        $this->addSql('CREATE INDEX IDX_9171FC29D86650F ON don_history (user_id_id)');
        $this->addSql('CREATE INDEX IDX_9171FC29AB84EE2 ON don_history (funds_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE don_history DROP FOREIGN KEY FK_9171FC29D86650F');
        $this->addSql('ALTER TABLE don_history DROP FOREIGN KEY FK_9171FC29AB84EE2');
        $this->addSql('DROP INDEX IDX_9171FC29D86650F ON don_history');
        $this->addSql('DROP INDEX IDX_9171FC29AB84EE2 ON don_history');
        $this->addSql('ALTER TABLE don_history DROP user_id_id, DROP funds_id_id');
    }
}
