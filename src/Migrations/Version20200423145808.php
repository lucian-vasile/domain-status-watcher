<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200423145808 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE domains ADD COLUMN raw_whois_response CLOB DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__domains AS SELECT id, domain, current_status, checked_at, expires_at, is_owned FROM domains');
        $this->addSql('DROP TABLE domains');
        $this->addSql('CREATE TABLE domains (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, domain VARCHAR(191) NOT NULL, current_status VARCHAR(191) DEFAULT NULL, checked_at DATETIME DEFAULT NULL, expires_at DATETIME DEFAULT NULL, is_owned BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO domains (id, domain, current_status, checked_at, expires_at, is_owned) SELECT id, domain, current_status, checked_at, expires_at, is_owned FROM __temp__domains');
        $this->addSql('DROP TABLE __temp__domains');
    }
}
