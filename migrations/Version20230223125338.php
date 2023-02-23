<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223125338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fundrising ADD don_history_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fundrising ADD CONSTRAINT FK_FB33B45DBAE30C4E FOREIGN KEY (don_history_id) REFERENCES don_history (id)');
        $this->addSql('CREATE INDEX IDX_FB33B45DBAE30C4E ON fundrising (don_history_id)');
        $this->addSql('ALTER TABLE user ADD don_history_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BAE30C4E FOREIGN KEY (don_history_id) REFERENCES don_history (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649BAE30C4E ON user (don_history_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fundrising DROP FOREIGN KEY FK_FB33B45DBAE30C4E');
        $this->addSql('DROP INDEX IDX_FB33B45DBAE30C4E ON fundrising');
        $this->addSql('ALTER TABLE fundrising DROP don_history_id');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649BAE30C4E');
        $this->addSql('DROP INDEX IDX_8D93D649BAE30C4E ON `user`');
        $this->addSql('ALTER TABLE `user` DROP don_history_id');
    }
}
