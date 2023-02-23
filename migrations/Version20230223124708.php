<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223124708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fundrising_participant_don DROP FOREIGN KEY FK_69A6833D3D5C7282');
        $this->addSql('ALTER TABLE fundrising_participant_don DROP FOREIGN KEY FK_69A6833DC9402EE0');
        $this->addSql('DROP TABLE fundrising_participant_don');
        $this->addSql('DROP TABLE participant_don');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fundrising_participant_don (fundrising_id INT NOT NULL, participant_don_id INT NOT NULL, INDEX IDX_69A6833DC9402EE0 (participant_don_id), INDEX IDX_69A6833D3D5C7282 (fundrising_id), PRIMARY KEY(fundrising_id, participant_don_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE participant_don (id INT AUTO_INCREMENT NOT NULL, price DOUBLE PRECISION NOT NULL, date_participant DATE NOT NULL, comment VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE fundrising_participant_don ADD CONSTRAINT FK_69A6833D3D5C7282 FOREIGN KEY (fundrising_id) REFERENCES fundrising (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fundrising_participant_don ADD CONSTRAINT FK_69A6833DC9402EE0 FOREIGN KEY (participant_don_id) REFERENCES participant_don (id) ON DELETE CASCADE');
    }
}
