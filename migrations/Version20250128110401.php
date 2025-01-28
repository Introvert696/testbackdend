<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250128110401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE procedure_list DROP FOREIGN KEY FK_5C7CA090CEC3FD2F');
        $this->addSql('DROP INDEX IDX_5C7CA090CEC3FD2F ON procedure_list');
        $this->addSql('ALTER TABLE procedure_list ADD source_id VARCHAR(255) DEFAULT NULL, ADD source_type VARCHAR(255) DEFAULT NULL, DROP patients_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE procedure_list ADD patients_id INT DEFAULT NULL, DROP source_id, DROP source_type');
        $this->addSql('ALTER TABLE procedure_list ADD CONSTRAINT FK_5C7CA090CEC3FD2F FOREIGN KEY (patients_id) REFERENCES patients (id)');
        $this->addSql('CREATE INDEX IDX_5C7CA090CEC3FD2F ON procedure_list (patients_id)');
    }
}
