<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250123075902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chambers (id INT AUTO_INCREMENT NOT NULL, number INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chambers_patients (id INT AUTO_INCREMENT NOT NULL, chambers_id INT NOT NULL, patients_id INT NOT NULL, INDEX IDX_E24B50C047FF4606 (chambers_id), UNIQUE INDEX UNIQ_E24B50C0CEC3FD2F (patients_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patients (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, card_number INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE procedure_list (id INT AUTO_INCREMENT NOT NULL, procedures_id INT NOT NULL, patients_id INT DEFAULT NULL, queue INT DEFAULT NULL, status TINYINT(1) NOT NULL, INDEX IDX_5C7CA0908FBA2A61 (procedures_id), INDEX IDX_5C7CA090CEC3FD2F (patients_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE procedures (id INT AUTO_INCREMENT NOT NULL, tite VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chambers_patients ADD CONSTRAINT FK_E24B50C047FF4606 FOREIGN KEY (chambers_id) REFERENCES chambers (id)');
        $this->addSql('ALTER TABLE chambers_patients ADD CONSTRAINT FK_E24B50C0CEC3FD2F FOREIGN KEY (patients_id) REFERENCES patients (id)');
        $this->addSql('ALTER TABLE procedure_list ADD CONSTRAINT FK_5C7CA0908FBA2A61 FOREIGN KEY (procedures_id) REFERENCES procedures (id)');
        $this->addSql('ALTER TABLE procedure_list ADD CONSTRAINT FK_5C7CA090CEC3FD2F FOREIGN KEY (patients_id) REFERENCES patients (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambers_patients DROP FOREIGN KEY FK_E24B50C047FF4606');
        $this->addSql('ALTER TABLE chambers_patients DROP FOREIGN KEY FK_E24B50C0CEC3FD2F');
        $this->addSql('ALTER TABLE procedure_list DROP FOREIGN KEY FK_5C7CA0908FBA2A61');
        $this->addSql('ALTER TABLE procedure_list DROP FOREIGN KEY FK_5C7CA090CEC3FD2F');
        $this->addSql('DROP TABLE chambers');
        $this->addSql('DROP TABLE chambers_patients');
        $this->addSql('DROP TABLE patients');
        $this->addSql('DROP TABLE procedure_list');
        $this->addSql('DROP TABLE procedures');
    }
}
