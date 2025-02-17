<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217133443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambers_patients DROP CONSTRAINT FK_E24B50C0CEC3FD2F');
        $this->addSql('ALTER TABLE chambers_patients ADD CONSTRAINT FK_E24B50C0CEC3FD2F FOREIGN KEY (patients_id) REFERENCES patients (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE chambers_patients DROP CONSTRAINT fk_e24b50c0cec3fd2f');
        $this->addSql('ALTER TABLE chambers_patients ADD CONSTRAINT fk_e24b50c0cec3fd2f FOREIGN KEY (patients_id) REFERENCES patients (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
