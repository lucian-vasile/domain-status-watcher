<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200424181800 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__domains AS SELECT id, domain, current_status, checked_at, expires_at, is_owned, raw_whois_response FROM domains');
        $this->addSql('DROP TABLE domains');
        $this->addSql('CREATE TABLE domains (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, domain VARCHAR(191) NOT NULL COLLATE BINARY, current_status VARCHAR(191) DEFAULT NULL COLLATE BINARY, checked_at DATETIME DEFAULT NULL, expires_at DATETIME DEFAULT NULL, is_owned BOOLEAN NOT NULL, raw_whois_response CLOB DEFAULT NULL --(DC2Type:json)
        )');
        $this->addSql('INSERT INTO domains (id, domain, current_status, checked_at, expires_at, is_owned, raw_whois_response) SELECT id, domain, current_status, checked_at, expires_at, is_owned, raw_whois_response FROM __temp__domains');
        $this->addSql('DROP TABLE __temp__domains');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8C7BBF9DA7A91E0B ON domains (domain)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX UNIQ_8C7BBF9DA7A91E0B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__domains AS SELECT id, domain, current_status, checked_at, expires_at, is_owned, raw_whois_response FROM domains');
        $this->addSql('DROP TABLE domains');
        $this->addSql('CREATE TABLE domains (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, domain VARCHAR(191) NOT NULL, current_status VARCHAR(191) DEFAULT NULL, checked_at DATETIME DEFAULT NULL, expires_at DATETIME DEFAULT NULL, is_owned BOOLEAN NOT NULL, raw_whois_response CLOB DEFAULT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO domains (id, domain, current_status, checked_at, expires_at, is_owned, raw_whois_response) SELECT id, domain, current_status, checked_at, expires_at, is_owned, raw_whois_response FROM __temp__domains');
        $this->addSql('DROP TABLE __temp__domains');
    }
}
