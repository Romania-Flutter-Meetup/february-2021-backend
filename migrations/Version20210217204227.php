<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210217204227 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE apartments (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, number VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, entry VARCHAR(255) NOT NULL, INDEX IDX_7745248EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monthly_payments (id INT AUTO_INCREMENT NOT NULL, apartment_id INT NOT NULL, cold_water DOUBLE PRECISION NOT NULL, hot_water DOUBLE PRECISION NOT NULL, total DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_24DAB4E9176DFE85 (apartment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE test_entity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, number INT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, name VARCHAR(50) NOT NULL, phone_number VARCHAR(50) DEFAULT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, is_email_confirmed TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE water_consumptions (id INT AUTO_INCREMENT NOT NULL, apartment_id INT NOT NULL, bathroom_hot DOUBLE PRECISION NOT NULL, bathroom_cold DOUBLE PRECISION NOT NULL, kitchen_hot DOUBLE PRECISION NOT NULL, kitchen_cold DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_409DE087176DFE85 (apartment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE apartments ADD CONSTRAINT FK_7745248EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE monthly_payments ADD CONSTRAINT FK_24DAB4E9176DFE85 FOREIGN KEY (apartment_id) REFERENCES apartments (id)');
        $this->addSql('ALTER TABLE water_consumptions ADD CONSTRAINT FK_409DE087176DFE85 FOREIGN KEY (apartment_id) REFERENCES apartments (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE monthly_payments DROP FOREIGN KEY FK_24DAB4E9176DFE85');
        $this->addSql('ALTER TABLE water_consumptions DROP FOREIGN KEY FK_409DE087176DFE85');
        $this->addSql('ALTER TABLE apartments DROP FOREIGN KEY FK_7745248EA76ED395');
        $this->addSql('DROP TABLE apartments');
        $this->addSql('DROP TABLE monthly_payments');
        $this->addSql('DROP TABLE test_entity');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE water_consumptions');
    }
}
