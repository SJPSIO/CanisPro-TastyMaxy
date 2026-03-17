<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260317103159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription ADD chien_id INT NOT NULL, ADD seance_id INT NOT NULL');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6BFCF400E FOREIGN KEY (chien_id) REFERENCES chien (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6E3797A94 FOREIGN KEY (seance_id) REFERENCES seance (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_5E90F6D6BFCF400E ON inscription (chien_id)');
        $this->addSql('CREATE INDEX IDX_5E90F6D6E3797A94 ON inscription (seance_id)');
        $this->addSql('ALTER TABLE seance DROP FOREIGN KEY `FK_DF7DFD0E7ECF78B0`');
        $this->addSql('ALTER TABLE seance ADD lieu VARCHAR(255) NOT NULL, ADD nb_places_max INT NOT NULL, CHANGE cours_id cours_id INT NOT NULL');
        $this->addSql('ALTER TABLE seance ADD CONSTRAINT FK_DF7DFD0E7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6BFCF400E');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6E3797A94');
        $this->addSql('DROP INDEX IDX_5E90F6D6BFCF400E ON inscription');
        $this->addSql('DROP INDEX IDX_5E90F6D6E3797A94 ON inscription');
        $this->addSql('ALTER TABLE inscription DROP chien_id, DROP seance_id');
        $this->addSql('ALTER TABLE seance DROP FOREIGN KEY FK_DF7DFD0E7ECF78B0');
        $this->addSql('ALTER TABLE seance DROP lieu, DROP nb_places_max, CHANGE cours_id cours_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE seance ADD CONSTRAINT `FK_DF7DFD0E7ECF78B0` FOREIGN KEY (cours_id) REFERENCES cours (id)');
    }
}
