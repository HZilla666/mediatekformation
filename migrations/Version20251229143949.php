<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251229143949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categorie CHANGE name name VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE categorie RENAME INDEX name TO UNIQ_497DD6345E237E06');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D782112D5E237E06 ON playlist (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE categorie CHANGE name name VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE categorie RENAME INDEX uniq_497dd6345e237e06 TO name');
        $this->addSql('DROP INDEX UNIQ_D782112D5E237E06 ON playlist');
    }
}
