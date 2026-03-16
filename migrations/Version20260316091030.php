<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260316091030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chien ADD proprietaire_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chien ADD CONSTRAINT FK_13A4067E76C50E4A FOREIGN KEY (proprietaire_id) REFERENCES proprietaire (id)');
        $this->addSql('CREATE INDEX IDX_13A4067E76C50E4A ON chien (proprietaire_id)');
        $this->addSql('ALTER TABLE proprietaire ADD utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE proprietaire ADD CONSTRAINT FK_69E399D6FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_69E399D6FB88E14F ON proprietaire (utilisateur_id)');
        $this->addSql('ALTER TABLE seance ADD cours_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE seance ADD CONSTRAINT FK_DF7DFD0E7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_DF7DFD0E7ECF78B0 ON seance (cours_id)');
        $this->addSql('ALTER TABLE utilisateur ADD proprietaire_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B376C50E4A FOREIGN KEY (proprietaire_id) REFERENCES proprietaire (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B376C50E4A ON utilisateur (proprietaire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chien DROP FOREIGN KEY FK_13A4067E76C50E4A');
        $this->addSql('DROP INDEX IDX_13A4067E76C50E4A ON chien');
        $this->addSql('ALTER TABLE chien DROP proprietaire_id');
        $this->addSql('ALTER TABLE proprietaire DROP FOREIGN KEY FK_69E399D6FB88E14F');
        $this->addSql('DROP INDEX UNIQ_69E399D6FB88E14F ON proprietaire');
        $this->addSql('ALTER TABLE proprietaire DROP utilisateur_id');
        $this->addSql('ALTER TABLE seance DROP FOREIGN KEY FK_DF7DFD0E7ECF78B0');
        $this->addSql('DROP INDEX IDX_DF7DFD0E7ECF78B0 ON seance');
        $this->addSql('ALTER TABLE seance DROP cours_id');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B376C50E4A');
        $this->addSql('DROP INDEX UNIQ_1D1C63B376C50E4A ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur DROP proprietaire_id');
    }
}
