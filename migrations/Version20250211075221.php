<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250211075221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE chambers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE chambers_patients_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE patients_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE procedure_list_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE procedures_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE chambers (id INT NOT NULL, number INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2A23180196901F54 ON chambers (number)');
        $this->addSql('CREATE TABLE chambers_patients (id INT NOT NULL, chambers_id INT DEFAULT NULL, patients_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E24B50C047FF4606 ON chambers_patients (chambers_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E24B50C0CEC3FD2F ON chambers_patients (patients_id)');
        $this->addSql('CREATE TABLE patients (id INT NOT NULL, name VARCHAR(255) NOT NULL, card_number INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2CCC2E2CE4AF4C20 ON patients (card_number)');
        $this->addSql('CREATE TABLE procedure_list (id INT NOT NULL, procedures_id INT NOT NULL, queue INT DEFAULT NULL, source_id VARCHAR(255) DEFAULT NULL, source_type VARCHAR(255) DEFAULT NULL, status BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5C7CA0908FBA2A61 ON procedure_list (procedures_id)');
        $this->addSql('CREATE TABLE procedures (id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_969AFE422B36786B ON procedures (title)');
        $this->addSql('ALTER TABLE chambers_patients ADD CONSTRAINT FK_E24B50C047FF4606 FOREIGN KEY (chambers_id) REFERENCES chambers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE chambers_patients ADD CONSTRAINT FK_E24B50C0CEC3FD2F FOREIGN KEY (patients_id) REFERENCES patients (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE procedure_list ADD CONSTRAINT FK_5C7CA0908FBA2A61 FOREIGN KEY (procedures_id) REFERENCES procedures (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE chambers_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE chambers_patients_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE patients_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE procedure_list_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE procedures_id_seq CASCADE');
        $this->addSql('ALTER TABLE chambers_patients DROP CONSTRAINT FK_E24B50C047FF4606');
        $this->addSql('ALTER TABLE chambers_patients DROP CONSTRAINT FK_E24B50C0CEC3FD2F');
        $this->addSql('ALTER TABLE procedure_list DROP CONSTRAINT FK_5C7CA0908FBA2A61');
        $this->addSql('DROP TABLE chambers');
        $this->addSql('DROP TABLE chambers_patients');
        $this->addSql('DROP TABLE patients');
        $this->addSql('DROP TABLE procedure_list');
        $this->addSql('DROP TABLE procedures');
    }
}
