<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260207153442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE business_partners (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, legal_form VARCHAR(255) NOT NULL, address VARCHAR(70) NOT NULL, city VARCHAR(35) NOT NULL, zip VARCHAR(16) NOT NULL, country VARCHAR(2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accounts (id INT AUTO_INCREMENT NOT NULL, business_partner_id INT NOT NULL, currency VARCHAR(255) NOT NULL, balance NUMERIC(10, 2) NOT NULL, INDEX IDX_CAC89EAC5330F055 (business_partner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transactions (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, amount NUMERIC(10, 2) NOT NULL, name VARCHAR(255) NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', executed TINYINT(1) NOT NULL, type VARCHAR(50) NOT NULL, country VARCHAR(2) NOT NULL, iban VARCHAR(34) NOT NULL, INDEX IDX_EAA81A4C9B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE accounts ADD CONSTRAINT FK_CAC89EAC5330F055 FOREIGN KEY (business_partner_id) REFERENCES business_partners (id)');
        $this->addSql('ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4C9B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE accounts DROP FOREIGN KEY FK_CAC89EAC5330F055');
        $this->addSql('ALTER TABLE transactions DROP FOREIGN KEY FK_EAA81A4C9B6B5FBA');
        $this->addSql('DROP TABLE accounts');
        $this->addSql('DROP TABLE business_partners');
        $this->addSql('DROP TABLE transactions');
    }
}
